import 'dart:convert';
import 'dart:developer';

import 'package:doctorq/app_export.dart';
import 'package:doctorq/extensions.dart';
import 'package:doctorq/screens/appointments/past_appointments/past_appointments.dart';
import 'package:doctorq/screens/appointments/upcoming_appointments/UpcomingAppointments.dart';
import 'package:doctorq/services/api_service.dart';
import 'package:doctorq/stores/user_store.dart';
import 'package:doctorq/utils/utility.dart';
import 'package:doctorq/widgets/loading_overlay.dart';
import 'package:doctorq/widgets/spacing.dart';
import 'package:doctorq/widgets/top_back.dart';
import 'package:flutter/material.dart';
import 'package:graphql/client.dart';
//import 'package:flutter_inappwebview/flutter_inappwebview.dart';

import '../../utils/pub.dart';

import 'package:get_it/get_it.dart';

GetIt getIt = GetIt.instance;

class AppointmentsScreen extends StatefulWidget {
  final String? mode;

  const AppointmentsScreen({Key? key, this.mode}) : super(key: key);
  @override
  State<AppointmentsScreen> createState() => _AppointmentsScreenState();
}

class _AppointmentsScreenState extends State<AppointmentsScreen> {
  bool showUpcomming = true;
//  bool showUpcomming = widget.mode == 'old' ? false : true;
  List myAppointments = [];

  @override
  initState() {
    super.initState();
    showUpcomming = widget.mode == 'old' ? false : true;
    loadData();

    /*   pullToRefreshController = PullToRefreshController(
        options: PullToRefreshOptions(
          color: Colors.blue,
        ),
        onRefresh: () async {
          print("refresh");
        });*/
  }

  loadData() async {
    printLog('Getting Appointments');
/*
    Future.delayed(Duration.zero, () {
      MyOverlay.show(context);
    });
*/
    UserStore storeUserStore = getIt.get<UserStore>();
    Map<dynamic, dynamic> userData = storeUserStore.userData;
    print(userData);
    // пример того как грузить много данных
    List<bool> resultOfRequests = await Future.wait([
      userData['patient_id'] != null
          ? getAppointments(patientId: userData['patient_id'])
          : getAppointmentsD(doctorId: userData['doctor_id'])
    ]);

    setState(() {
      myAppointments = context.appointmentsData;
    });

    inspect(resultOfRequests);

    printLog('Appointments loaded');

    //MyOverlay.hide();
  }

//  late PullToRefreshController pullToRefreshController;
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            ...topBack(
                height: 0, text: "Записи", context: context, back: false),
            // const HeaderNavBar(),
            HeaderFilterButtons(),
            VerticalSpace(height: 24),
            Expanded(
                child: RefreshIndicator(
              displacement: 1.0,
              onRefresh: () async {
                print("refreshing??");
                //await itemController.refreshData();
                await Future.delayed(const Duration(seconds: 2));
                await loadData();
                setState(() {
                  myAppointments = context.appointmentsData;
                }); // Simulate network request
              },
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child:
                    showUpcomming ? 
                    UpcomingAppointments() : PastAppointments(),
              ),
            )),
          ],
        ),
      ),
    );
  }

  Container HeaderFilterButtons() {
    return Container(
      alignment: Alignment.center,
      decoration: BoxDecoration(
        color: ColorConstant.fromHex("E4F0FF"),
        borderRadius: BorderRadius.circular(
          getHorizontalSize(
            24.0,
          ),
        ),
      ),
      // color: ColorConstant.fromHex("E4F0FF"),
      height: getVerticalSize(
        45.00,
      ),
      // margin: getMargin(left: 20, top: 24),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Expanded(
            child: InkWell(
              onTap: () {
                setState(() {
                  showUpcomming = true;
                });
              },
              child: Container(
                padding: getPadding(top: 8, bottom: 8),
                //padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: showUpcomming ? Colors.white : Colors.transparent,
                  borderRadius: BorderRadius.circular(
                    getHorizontalSize(
                      24.0,
                    ),
                  ),
                  /* border: Border.all(
                    color: ColorConstant.blueA400,
                    width: getHorizontalSize(
                      0.00,
                    ),
                  ),*/
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Text(
                      'Предстоящие',
                      style: TextStyle(
                          fontFamily: 'Source Sans Pro',
                          fontSize: getFontSize(12),
                          fontWeight: FontWeight.w600,
                          color: showUpcomming
                              ? Colors.black
                              : ColorConstant.blueA400),
                    ),
                  ],
                ),
              ),
            ),
          ),
          HorizontalSpace(width: 10),
          Expanded(
            child: InkWell(
              onTap: () {
                setState(() {
                  showUpcomming = false;
                });
              },
              child: Container(
                padding: getPadding(top: 8, bottom: 8),
                decoration: BoxDecoration(
                  color: !showUpcomming ? Colors.white : Colors.transparent,
                  borderRadius: BorderRadius.circular(
                    getHorizontalSize(
                      24.0,
                    ),
                  ),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Text(
                      'Прошедшие',
                      style: TextStyle(
                          fontFamily: 'Source Sans Pro',
                          fontSize: getFontSize(12),
                          fontWeight: FontWeight.w600,
                          color: showUpcomming
                              ? Colors.black
                              : ColorConstant.blueA400),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
