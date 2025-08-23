import 'package:doctorq/screens/first/first.dart';
import 'package:doctorq/theme/app_style.dart';
import 'package:doctorq/widgets/custom_button.dart';
import 'package:flutter/material.dart';
import 'package:doctorq/screens/first/third.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';

class secondScreen extends StatelessWidget {
  const secondScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
        home: GestureDetector(
      onHorizontalDragEnd: (details) {
        if (details.primaryVelocity! > 0) {
          // User swiped left
          Navigator.pop(context);
        } else {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (context) => const thirdScreen()),
          );
        }
      },
      child: Scaffold(
        body: CustomContainer(),
      ),
    ));
  }
}

class CustomContainer extends StatelessWidget {
  final String imageUrl =
      'assets/images/a_gal.jpg'; // Replace with your image path
  final String bottomText =
      'Возможность записи на онлайн-консультацию записаться к врачу 24/7';

  @override
  Widget build(BuildContext context) {
    bool isDark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      height: MediaQuery.of(context).size.height,
      clipBehavior: Clip.antiAlias,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        image: DecorationImage(
          alignment: Alignment(0.2, 0),
          image: AssetImage(imageUrl),
          fit: BoxFit.fitHeight,
        ),
      ),
      child: Stack(children: [
        // Gradient overlay
        Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              stops: [0, 0.7, 1],
              colors: [
                Colors.white.withOpacity(0),
                Color(0x6B1A5F7E),
                Color(0xFF1C5E7E)
              ],
            ),
          ),
        ),

        Positioned(
            left: 24.w,
            bottom: 16.h + 34.h,
            right: 24.w,
            child: Column(
              children: [
                Text(
                  bottomText,
                  style: AppStyle.txt20lh24w700hexFAFAFA,
                ),
                SizedBox(height: 24.h),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        ...List.generate(
                          4,
                          (_) => Container(
                            // child: Text(_.toString()),
                            margin: EdgeInsets.only(right: 8.h),
                            width: 12.w,
                            height: 12.h,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              color: _ == 1 ? Colors.blue : Colors.white,
                            ),
                          ),
                        ),
                      ],
                    ),
                    CustomButton(
                      text: "Далее",
                      width: 145.w,
                      padding: ButtonPadding.PaddingH10V12,
                      isDark: isDark,
                      shape: ButtonShape.RoundedBorder20,
                      textStyle: AppStyle.txt16h18w600hex09101D,
                      variant: ButtonVariant.Default,
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const thirdScreen(),
                          ),
                        );
                      },
                    ),
                  ],
                )
              ],
            ))
      ]),
    );
  }
}
