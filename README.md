jSignature Draw
===============
Renders a Signature Pad using jSignature

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist fvy/yii2-signature-draw: "dev-master"
```

or add

```
"fvy/yii2-signature-draw": "dev-master"
```

to the require section of your `composer.json` file.

composer update fvy/yii2-signature-draw will only update this file.


Usage
-----

The js files are from brinley.github.io See [demos here](http://brinley.github.io/jSignature/ "Signature Capture Demos").

Once the extension is installed, simply use it in your code by  :

```php
Create a jSignature Canvas.
<?= \fvy\signaturedraw\SignatureDraw::widget(); ?>

You can use your own view file 
<?= \fvy\signaturedraw\SignatureDraw::widget(['view'=>'\\pathto\file']); ?>
It is recommended to change your own file.

In the file under views/sdv.php
you can easily modify your .css, javascript and canvas options.
