<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDoctorSessionRequest;
use App\Http\Requests\UpdateDoctorSessionRequest;
use App\Models\Appointment;
use App\Models\ClinicSchedule;
use App\Models\DoctorHoliday;
use App\Models\DoctorSession;
use App\Models\WeekDay;
use App\Repositories\DoctorSessionRepository;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Laracasts\Flash\Flash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class DoctorSessionController extends AppBaseController
{
    /** @var DoctorSessionRepository */
    private $doctorSessionRepository;

    public function __construct(DoctorSessionRepository $doctorSessionRepo)
    {
        $this->doctorSessionRepository = $doctorSessionRepo;
    }

    /**
     * Display a listing of the DoctorSession.
     *
     * @return Application|Factory|View
     */
    public function index(): \Illuminate\View\View
    {
        return view('doctor_sessions.index');
    }

    /**
     * Show the form for creating a new DoctorSession.
     *
     * @return Application|Factory|View
     */
    public function create(): \Illuminate\View\View
    {
        $doctorsList = $this->doctorSessionRepository->getSyncList();

        return view('doctor_sessions.create', compact('doctorsList'));
    }

    /**
     * Store a newly created DoctorSession in storage.
     */
    public function store(CreateDoctorSessionRequest $request)
    {
        $input = $request->all();

        $result = $this->doctorSessionRepository->store($input);

        if (! $result['success']) {
            return $this->sendError($result);
        }

        return $this->sendSuccess(__('messages.flash.schedule_crete'));
    }

    /**
     * Display the specified DoctorSession.
     *
     * @return Application|Factory|View
     */
    public function show(DoctorSession $doctorSession)
    {
        if (empty($doctorSession)) {
            Flash::error(__('messages.flash.doctor_session_not_found'));

            return redirect(getDoctorSessionURL());
        }

        return view('doctor_sessions.show', compact('doctorSession'));
    }

    /**
     * Show the form for editing the specified DoctorSession.
     *
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        if (getLogInUser()->hasRole('doctor')) {
            $doctorSession = DoctorSession::with('doctor.user')->whereDoctorId(getLogInUser()->doctor->id)->first();
        } else {
            $doctorSession = DoctorSession::with('doctor.user')->findOrFail($id);
        }
        $doctorsList = $this->doctorSessionRepository->getSyncList();

        if (empty($doctorSession)) {
            Flash::error(__('messages.flash.schedule_not_found'));

            return redirect(route('doctor-sessions.index'));
        }

        $sessionWeekDays = $doctorSession->sessionWeekDays;

        return view('doctor_sessions.edit', compact('doctorSession', 'doctorsList', 'sessionWeekDays'));
    }

    /**
     * Update the specified DoctorSession in storage.
     */
    public function update(UpdateDoctorSessionRequest $request, DoctorSession $doctorSession): JsonResponse
    {
        if (empty($doctorSession)) {
            return $this->sendError(__('messages.flash.doctor_session_not_found'));
        }

        $result = $this->doctorSessionRepository->updateDoctorSession($request->all(), $doctorSession);

        if (! $result['success']) {
            return $this->sendError($result);
        }

        return $this->sendSuccess(__('messages.flash.schedule_update'));
    }

    /**
     * Remove the specified DoctorSession from storage.
     */
    public function destroy(DoctorSession $doctorSession): JsonResponse
    {
        try {
            DB::beginTransaction();

            $doctorSession->delete();

            $doctorSession->sessionWeekDays()->delete();

            DB::commit();

            return $this->sendSuccess(__('messages.flash.schedule_delete'));
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function getDoctorSession(Request $request): JsonResponse
    {

        $holidaydate = $request->get('date');
       /* if($holidaydate=='1-01-01')
        {
            $slots=  [1];
            return $this->sendResponse($slots, __('messages.flash.retrieve'));
        }*/
        $doctorId = $request->get('adminAppointmentDoctorId');
        $timezone_offset_minutes = $request->get('timezone_offset_minutes');
        $doctor_holiday = DoctorHoliday::where('doctor_id', $doctorId)->where('date', $holidaydate)->get();
        if (! $doctor_holiday->count() == 0) {
            return $this->sendError(__('messages.flash.doctor_not_available'));
        }
        // Convert minutes to seconds
        $timezone_name = timezone_name_from_abbr('', $timezone_offset_minutes * 60, false);
        $date = Carbon::createFromFormat('Y-m-d', $request->date);
        $doctorWeekDaySessions = WeekDay::whereDayOfWeek($date->dayOfWeek)->whereDoctorId($doctorId)->with('doctorSession')->get();
        if ($doctorWeekDaySessions->count() == 0) {
            if (! empty(getLogInUser()->language)) {
                App::setLocale(getLogInUser()->language);
            } else {
                App::setLocale($request->session()->get('languageName'));
            }

            return $this->sendError(__('messages.flash.no_available_slots'));
        }

        $appointments = Appointment::whereDoctorId($doctorId)->whereIn('status',
            [Appointment::BOOKED, Appointment::CHECK_IN, Appointment::CHECK_OUT])->get();
        $bookedSlot = [];
        $bookingSlot = [];
        foreach ($appointments as $appointment) {
            if ($appointment->date == $request->date) {
         //       $bookedSlot[] = $appointment->from_time.' '.$appointment->from_time_type.' - '.$appointment->to_time.' '.$appointment->to_time_type;
         $bookedSlot[] = date('H:i', strtotime($appointment->from_time)).' - '.date('H:i', strtotime($appointment->to_time));
         
            }
        }
//print_r($doctorWeekDaySessions);
foreach ($doctorWeekDaySessions as $doctorWeekDaySession) {
    date_default_timezone_set($timezone_name);
    $doctorSession = $doctorWeekDaySession->doctorSession;
    
    // Use direct times without conversion
    $startTime = $doctorWeekDaySession->full_start_time; // "13:17"
    $endTime = $doctorWeekDaySession->full_end_time;     // "22:09"
    
    $slots = $this->getTimeSlot($doctorSession->session_meeting_time, $startTime, $endTime);
    $gap = $doctorSession->session_gap;
    $isSameWeekDay = (Carbon::now()->dayOfWeek == $date->dayOfWeek) && (Carbon::now()->isSameDay($date));
    
    foreach ($slots as $slot) {
        $slotStart = $slot[0];
        $slotEnd = $slot[1];
        
        // Convert to 12-hour format for display if needed
        $displayStart = date('h:i A', strtotime($slotStart));
        $displayEnd = date('h:i A', strtotime($slotEnd));
        
        // Check if slot is valid
        if ($isSameWeekDay) {
            $currentTime = Carbon::now()->format('H:i');
            if ($slotStart < $currentTime) {
                continue; // Skip past slots for current day
            }
        }
        
        $bookingSlot[] = "$slotStart - $slotEnd";
    }
}/*
        foreach ($doctorWeekDaySessions as $index => $doctorWeekDaySession) {
            date_default_timezone_set($timezone_name);

            $doctorSession = $doctorWeekDaySession->doctorSession;
            // convert 12 hours to 24 hours
           //echo $startTime = date('H:i', strtotime($doctorWeekDaySession->full_start_time));
           //echo $endTime = date('H:i', strtotime($doctorWeekDaySession->full_end_time));
            $startTime = $doctorWeekDaySession->full_start_time; // "19:09"
            $endTime = $doctorWeekDaySession->full_end_time;     // "22:09"
           //exit();
            $slots = $this->getTimeSlot($doctorSession->session_meeting_time, $startTime, $endTime);
            echo $gap = $doctorSession->session_gap;
            $isSameWeekDay = (Carbon::now()->dayOfWeek == $date->dayOfWeek) && (Carbon::now()->isSameDay($date));
            foreach ($slots as $key => $slot) {
            //    echo "Processing Slot [slot => $slot[0],$slot[1] key => $key ]";
                $key--;
                if ($key != 0) {
                    $slotStartTime = date('h:i A',
                        strtotime('+'.$gap * $key.' minutes', strtotime($slot[0])));
                    $slotEndTime = date('h:i A',
                        strtotime('+'.$gap * $key.' minutes', strtotime($slot[1])));
                    if (strtotime($doctorWeekDaySession->full_end_time) < strtotime($slotEndTime)) {
                        break;
                    }
                    if (strtotime($slotStartTime) < strtotime($slotEndTime)) {
                        if (($isSameWeekDay && strtotime($slotStartTime) > strtotime(date('h:i A'))) || ! $isSameWeekDay) {
                            $startTimeOrg = Carbon::parse(date('h:i A', strtotime($slotStartTime)));
                            $slotStartTimeCarbon = Carbon::parse(date('h:i A', strtotime($startTime)));
                            $slotEndTimeCarbon = Carbon::parse(date('h:i A', strtotime($endTime)));
                            if (! $startTimeOrg->between($slotStartTimeCarbon, $slotEndTimeCarbon)) {
                                break;
                            }

                            if (in_array(($slotStartTime.' - '.$slotEndTime), $bookingSlot)) {
                                break;
                            }
                       //     $bookingSlot[] = $slotStartTime.' - '.$slotEndTime;
                       $bookingSlot[] = date('H:i', strtotime($slotStartTime)).' - '.date('H:i', strtotime($slotEndTime));
                        }
                    }
                } else {
                    if (($isSameWeekDay && strtotime($slot[0]) > strtotime(date('h:i A'))) || ! $isSameWeekDay) {
                        if (in_array((date('h:i A', strtotime($slot[0])).' - '.date('h:i A', strtotime($slot[1]))),
                            $bookingSlot)) {
                            break;
                        }
                      //  $bookingSlot[] = date('h:i A', strtotime($slot[0])).' - '.date('h:i A', strtotime($slot[1]));
                        $bookingSlot[] = date('H:i', strtotime($slot[0])).' - '.date('H:i', strtotime($slot[1]));

                    }
                }
            }
        }*/

        $slots = [
            'bookedSlot' => ! empty($bookedSlot) ? $bookedSlot : null,
            'slots' => $bookingSlot,
        ];

        return $this->sendResponse($slots, __('messages.flash.retrieve'));
    }
    public function getTimeSlot($interval, $start_time, $end_time)
{
    $start_time = preg_replace('/\s?[AP]M$/i', '', $start_time);
    $end_time = preg_replace('/\s?[AP]M$/i', '', $end_time);
    
    $start = Carbon::createFromFormat('H:i', $start_time);
    $end = Carbon::createFromFormat('H:i', $end_time);
    
    $time = [];
    $current = $start->copy();
    $i = 0;

    while ($current < $end) {
        $slotStart = $current->format('H:i');
        $current->addMinutes($interval);
        $slotEnd = $current->format('H:i');
        
        if ($current > $end) {
            break;
        }
        
        $time[++$i] = [$slotStart, $slotEnd];
    }

    return $time;
}
    /**
     * @throws Exception
     */
    public function getTimeSlot2($interval, $start_time, $end_time)
    {
        $start_time = preg_replace('/\s?[AP]M$/i', '', $start_time);
        $end_time = preg_replace('/\s?[AP]M$/i', '', $end_time);
        $start = new DateTime($start_time);
        $end = new DateTime($end_time);
        $carbonStart = Carbon::createFromFormat('H:i', $start_time);
        $carbonEnd = Carbon::createFromFormat('H:i', $end_time);
       
        $startTime = $start->format('H:i');
        $endTime = $originalEndTime = $end->format('H:i');
        $i = 0;
        $time = [];
       // echo $startTime;
      // echo ':';
      //  echo $endTime;
        while (strtotime($startTime) <= strtotime($endTime)) {
            $start = $startTime;
            $end = date('H:i', strtotime('+'.$interval.' minutes', strtotime($startTime)));
            $startTime = date('H:i', strtotime('+'.$interval.' minutes', strtotime($startTime)));
            if (! Carbon::createFromFormat('H:i', $start)->isBetween($carbonStart,
                $carbonEnd) || ! Carbon::createFromFormat('H:i', $end)->isBetween($carbonStart, $carbonEnd)) {

         //   echo 'breaking1';
                break;
            }
            $i++;
            if (strtotime($startTime) <= strtotime($endTime)) {
                $time[$i][] = $start;
                $time[$i][] = $end;
            }
            if (strtotime($startTime) >= strtotime($originalEndTime)) {
     //           echo 'breaking2';
                break;
            }
            if (strtotime($start) >= strtotime($end)) {
     //           echo 'breaking3';
                break;
            }
        }

        return $time;
    }

    /**
     * @return mixed
     */
    public function getSlotByGap(Request $request)
    {
        $gap = $request->get('gap');
        $day = $request->get('day');
        $clinicSchedule = ClinicSchedule::whereDayOfWeek($day)->first();
        $slots = getSlotByGap($clinicSchedule->start_time, $clinicSchedule->end_time);
        $html = view('doctor_sessions.slot', ['slots' => $slots, 'day' => $day])->render();

        return $this->sendResponse($html, __('messages.flash.retrieve'));
    }

    /**
     * @return Application|Factory|View|RedirectResponse|Redirector
     */
    public function doctorScheduleEdit()
    {
        $doctorSession = DoctorSession::whereDoctorId(getLogInUser()->doctor->id)->first();
        if (empty($doctorSession)) {
            Flash::error(__('messages.flash.schedule_not_found'));

            return redirect(route('doctor-sessions.index'));
        }

        $doctorsList = $this->doctorSessionRepository->getSyncList();

        $sessionWeekDays = $doctorSession->sessionWeekDays;

        return view('doctor_sessions.edit', compact('doctorSession', 'doctorsList', 'sessionWeekDays'));
    }
}
