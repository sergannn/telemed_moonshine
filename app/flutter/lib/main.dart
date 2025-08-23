import 'dart:developer';
import 'package:doctorq/numScreen.dart';
import 'package:doctorq/screens/auth/forgot/password_otp_active_screen/guess_code_screen.dart';
import 'package:doctorq/screens/auth/sign_in_blank_screen/doctor_screen.dart';
import 'package:doctorq/screens/auth/sign_in_blank_screen/sign_in_blank_screen.dart';
import 'package:doctorq/screens/first/figmasample.dart';
import 'package:doctorq/screens/first/first.dart';
import 'package:doctorq/screens/webviews/someWebPage.dart';
import 'package:doctorq/services/api_service.dart';
import 'package:doctorq/services/session.dart';
import 'package:doctorq/services/startup_service.dart';
import 'package:doctorq/stores/init_stores.dart';
import 'package:doctorq/stores/user_store.dart';
import 'package:doctorq/translations/codegen_loader.g.dart';
import 'package:doctorq/utils/utility.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:loader_overlay/loader_overlay.dart';
import 'package:number_pad_keyboard/number_pad_keyboard.dart';
import 'package:permission_handler/permission_handler.dart';
import 'theme/theme_constants.dart';
import 'theme/theme_manager.dart';
import 'package:doctorq/screens/main_screen.dart';
import 'package:another_flutter_splash_screen/another_flutter_splash_screen.dart';

import 'package:flutter_screenutil/flutter_screenutil.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
//  WidgetsFlutterBinding.ensureInitialized();

  final FlutterLocalNotificationsPlugin flutterLocalNotificationsPlugin =
      FlutterLocalNotificationsPlugin();

  const AndroidInitializationSettings initializationSettingsAndroid =
      AndroidInitializationSettings('@mipmap/ic_launcher');

  final InitializationSettings initializationSettings = InitializationSettings(
    android: initializationSettingsAndroid,
    iOS: DarwinInitializationSettings(),
  );

  await flutterLocalNotificationsPlugin.initialize(
    initializationSettings,
  );
  await EasyLocalization.ensureInitialized();

  SystemChrome.setPreferredOrientations([
//    DeviceOrientation.portraitUp,
  ]);
  await Session.init();

  initStores();
  await Future.delayed(Duration(milliseconds: 1000));
  runApp(MaterialApp(
      routes: {
        // When navigating to the "/" route, build the FirstScreen widget.
        //'/': (context) => const FirstScreen(),
        // When navigating to the "/second" route, build the SecondScreen widget.
        '/webview': (context) => const SomeWebView(),
      },
      title: "App",
      theme: ThemeData(
        scaffoldBackgroundColor: Colors.white,
        colorScheme: ColorScheme.fromSeed(
          seedColor: Colors.blue,
          surface: Colors.white,
          background: Colors.white,
        ),
      ),
      home: EasyLocalization(
          supportedLocales: const [Locale("en"), Locale("ar")],
          path: "assets/translations",
          assetLoader: const CodegenLoader(),
          fallbackLocale: const Locale('en'),
          child: MyApp())));
}

Future<void> onSelectNotification(String? payload) async {
  if (payload != null) {
    debugPrint('Notification payload: $payload');
  }
}

ThemeManager themeManager = ThemeManager();

class MyApp extends StatefulWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  State<MyApp> createState() => _MyAppState();
}

class _MyAppState extends State<MyApp> with WidgetsBindingObserver {
  final _passwordController = TextEditingController();

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    super.didChangeAppLifecycleState(state);

    switch (state) {
      case AppLifecycleState.resumed:
        debugPrint("App resumed");
        /*    Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => Numscreen()),
        );
*/
        print('resumed');
        break;
      case AppLifecycleState.paused:
        debugPrint("App paused");
        break;
      case AppLifecycleState.detached:
        debugPrint("App detached");
        break;
      case AppLifecycleState.inactive:
        debugPrint("App inactive");
        break;
      case AppLifecycleState.hidden:
        print("hidden");
      // TODO: Handle this case.
      //throw UnimplementedError();
    }
  }

  @override
  void initState() {
// TODO: implement initState
    super.initState();
    print('Context available: ${context != null}');
    themeManager.addListener(themeListener);

    WidgetsBinding.instance.addObserver(this);
    // _requestPermissions();
    // _configureDidReceiveLocalNotificationSubject();
    // _configureSelectNotificationSubject();
    // _handleIncomingLinks();
  }

  @override
  void dispose() {
// TODO: implement dispose

    themeManager.removeListener(themeListener);

    // didReceiveLocalNotificationSubject.close();
    // selectNotificationSubject.close();

    super.dispose();
  }

  void themeListener() {
    if (mounted) setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    return ScreenUtilInit(
      designSize: const Size(360, 800),
      builder: (context, child) {
        return GlobalLoaderOverlay(child: MaterialApp(
          theme: ThemeData(
            scaffoldBackgroundColor: Colors.white,
            colorScheme: ColorScheme.fromSeed(
              seedColor: Colors.blue,
              surface: Colors.white,
              background: Colors.white,
            ),
          ),
          //color: Colors.red,
          title: 'Телемедицина',
          debugShowCheckedModeBanner: false,
          //
          // theme: lightTheme,
          //  darkTheme: darkTheme,
          //  themeMode: themeManager.themeMode,
          localizationsDelegates: context.localizationDelegates,
          supportedLocales: context.supportedLocales,
          locale: context.locale,
          home: Builder(
            builder: (context) => FlutterSplashScreen.gif(
              gifHeight: MediaQuery.of(context).size.height,
              gifWidth: MediaQuery.of(context).size.width,
              gifPath: "./assets/images/a_logo.jpg",
              useImmersiveMode: true,
              backgroundColor: Colors.white,
              onInit: () => debugPrint("On Init"),
              onEnd: () => debugPrint("On End"),
              nextScreen: FutureBuilder(
                future: getStartupData(),
                builder: (BuildContext context, AsyncSnapshot<bool> snapshot) {
                  if (snapshot.connectionState == ConnectionState.done &&
                      snapshot.data == true) {
                    return Main();
                  }
                  return firstScreen();
                },
              ),
            ),
          ),
        ));
      },
    );
  }
}
