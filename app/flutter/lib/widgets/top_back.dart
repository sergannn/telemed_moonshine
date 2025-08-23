import 'package:flutter/material.dart';

List<Widget> topBack(
    {String text = '',
    required BuildContext context,
    double height = 40,
    Icon icon = const Icon(Icons.settings_input_component, size: 20),
    bool back = true}) {
  return [
    SizedBox(height: height), // фиксированный отступ
    Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color.fromARGB(255, 236, 236, 236).withOpacity(0.95),
        border: Border(bottom: BorderSide(color: Colors.grey.shade300)),
      ),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
        decoration: BoxDecoration(
          color: const Color.fromARGB(255, 255, 255, 255),
          borderRadius: BorderRadius.circular(20.0),
        ),
        child: Row(
          children: [
            IconButton(
         
              highlightColor: back ? Colors.orange.withOpacity(0.3) : Colors.transparent,
              icon: Icon(Icons.arrow_back_ios,
                  color: back ? Colors.black : Colors.transparent),
              onPressed: () { if(back) {Navigator.pop(context);}},
            ),
            Text(
              text,
              style: TextStyle(fontSize: 16),
            ),
            const Spacer(), // раздвигает элементы
            IconButton(
              icon:
                  icon, //const Icon(Icons.settings_input_component, size: 20),
              onPressed: () {}, //обработчик нажатия
            ),
          ],
        ),
      ),
    )
  ];
}
