# mtool_jap_sugoi_translation
Easy way to translate a json with mtool and play any game.


Instruction

First step you will need to execute php you can use alternative if you want but I will give an example:

1. Install XAMPP https://www.apachefriends.org/
2. Install Sugoi Translation Toolkit at https://www.patreon.com/mingshiba/about
3. Put this project code into the php folder where you installed your xampp example: D:\xampp\htdocs\

The setup is done

Now to translate use mtool, go to translate tab and click on export the original text which give you a ManualTransFile.json

Then but it in your php project folder 
<details>
  <summary>Image</summary>
  
![Capture](https://github.com/jamesbond448/mtool_jap_sugoi_translation/assets/32747767/476d7198-2e85-4ca0-b9a4-1224cb294e3c)
   
</details>

Now active XAMPP apache

<details>
  <summary>Image</summary>

![Capture](https://github.com/jamesbond448/mtool_jap_sugoi_translation/assets/32747767/8594f257-0e72-4836-8e0a-2036b6f4869a)

</details>

Then go to http://localhost/json_line/ 
which is the project and click on Extract.php

<details>
  <summary>Image</summary>

![Capture](https://github.com/jamesbond448/mtool_jap_sugoi_translation/assets/32747767/72d5b9bf-865a-4323-ace1-34202f2c95f6)

</details>


Then with sugoi translation toolkit click on button on bottom list named sugoi file translation

<details>
  <summary>Image</summary>

![Capture](https://github.com/jamesbond448/mtool_jap_sugoi_translation/assets/32747767/9651720a-f4e7-463b-9dfe-0b96c68b349b)

</details>

Then drag the file in extract folder named extracted (number).txt into the box of file translation

<details>
  <summary>Image</summary>

![Capture](https://github.com/jamesbond448/mtool_jap_sugoi_translation/assets/32747767/3637d930-a929-4356-8204-7156d205b12c)

</details>

Once done you now have a copy of those file in exract named extracted(number)_output.txt


Then go to http://localhost/json_line/ 
which is the project and click on Convert.php

This will give you translationDone.json

You now have a translation for mtool, just load this new translation file.

