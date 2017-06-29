<?php
//contains all the relevant javascript files and positions.
use fvy\signaturedraw\assets\SignatureDrawAsset;
SignatureDrawAsset::register($this);

//    $this->registerMetaTag(['name' => 'keywords', 'content' => 'yii, framework, php']);
//    $this->registerMetaTag(['charset' => 'utf-8']);
    //<meta charset="utf-8">
    $this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge,chrome=1']);
    //<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
//    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
    $this->registerMetaTag(['name'=>'viewport','content'=>'initial-scale=1.0','target-densitydpi'=>'device-dpi']);
//    <meta name="viewport" content="initial-scale=1.0, target-densitydpi=device-dpi" />
    $this->registerMetaTag(['name'=>'viewport','content'=>'initial-scale=1.0','width'=>'device-height']);
//    <meta name="viewport" content="initial-scale=1.0, width=device-height"><!--  mobile Safari, FireFox, Opera Mobile  -->
//    <!-- this is for mobile (Android) Chrome -->
//   Remove this if you use the .htaccess -->
    
    //http://stackoverflow.com/questions/26694675/store-jsignature-output-to-mysql-to-be-redrawn-on-separate-page
    
    //change the CSS to your needs.
    $this->registerCss("	
//        div {
//		margin-top:1em;
//		margin-bottom:1em;
//	}
//	input {
//		padding: .50em;
//		margin: .50em;
//	}
//	select {
//		padding: .5em;
//		margin: .5em;
//	}
	
	#signatureparent {
            color:darkblue;
            background-color:darkgrey;
            /*max-width:600px;*/
            padding:20px;
	}
	
	/*This is the div within which the signature canvas is fitted*/
	#signature {
		border: 2px dotted black;
		background-color:lightgrey;
	}

	/* Drawing the 'gripper' for touch-enabled devices */ 
	html.touch #content {
		float:left;
		width:92%;
	}
	html.touch #scrollgrabber {
		float:right;
		width:4%;
		margin-right:2%;
		background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAAFCAAAAACh79lDAAAAAXNSR0IArs4c6QAAABJJREFUCB1jmMmQxjCT4T/DfwAPLgOXlrt3IwAAAABJRU5ErkJggg==)
	}
	html.borderradius #scrollgrabber {
		border-radius: 1em;
	}
        " );
?>
    <!--WHERE The canvas is displayed-->
    <div id="signatureparent">
        <div id="signature"></div>
        <button type="button" id="btnClear">Clear</button>
        <button type="button" id="btnSave">Save</button>
    </div>
    <!--End Canvas Display-->
    <!--This is where the data value is captured to--> 
    <input type="hidden" id="hiddenSigData" name="hiddenSigData"/>
    <!--For testing only-->
    <textarea  rows="2" cols="150" id="textSigData" name="textSigData"></textarea>
    <!--The image display--> 
    <img id="imgSigData" name="imgSigData"  src=""  />
    
    <!--JavaScript Code change to your liking.-->
<script>
    $(document).ready(function() {
        var $sigdiv = $("#signature").jSignature({'UndoButton':true});
        $('#btnClear').click(function(){
            $('#signature').jSignature('clear');
            $('#hiddenSigData').val('');
            $('#textSigData').val('');
            $("#imgSigData").attr('src','');
        });
       
        $('#btnSave').click(function(){
            var emptySig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABgQAAAGBCAYAAACpTtriAAAgAElEQVR4Xu3ZQQEAAAgCMe1f2h7ebMDkx44jQIAAAQIECBAgQIAAAQIECBAgQIAAAQIE3gvs+4QCEiBAgAABAgQIECBAgAABAgQIECBAgAABAmMQUAICBAgQIECAAAECBAgQIECAAAECBAgQIBAQMAgEniwiAQIECBAgQIAAAQIECBAgQIAAAQIECBAwCOgAAQIECBAgQIAAAQIECBAgQIAAAQIECBAICBgEAk8WkQABAgQIECBAgAABAgQIECBAgAABAgQIGAR0gAABAgQIECBAgAABAgQIECBAgAABAgQIBAQMAoEni0iAAAECBAgQIECAAAECBAgQIECAAAECBAwCOkCAAAECBAgQIECAAAECBAgQIECAAAECBAICBoHAk0UkQIAAAQIECBAgQIAAAQIECBAgQIAAAQIGAR0gQIAAAQIECBAgQIAAAQIECBAgQIAAAQIBAYNA4MkiEiBAgAABAgQIECBAgAABAgQIECBAgAABg4AOECBAgAABAgQIECBAgAABAgQIECBAgACBgIBBIPBkEQkQIECAAAECBAgQIECAAAECBAgQIECAgEFABwgQIECAAAECBAgQIECAAAECBAgQIECAQEDAIBB4sogECBAgQIAAAQIECBAgQIAAAQIECBAgQMAgoAMECBAgQIAAAQIECBAgQIAAAQIECBAgQCAgYBAIPFlEAgQIECBAgAABAgQIECBAgAABAgQIECBgENABAgQIECBAgAABAgQIECBAgAABAgQIECAQEDAIBJ4sIgECBAgQIECAAAECBAgQIECAAAECBAgQMAjoAAECBAgQIECAAAECBAgQIECAAAECBAgQCAgYBAJPFpEAAQIECBAgQIAAAQIECBAgQIAAAQIECBgEdIAAAQIECBAgQIAAAQIECBAgQIAAAQIECAQEDAKBJ4tIgAABAgQIECBAgAABAgQIECBAgAABAgQMAjpAgAABAgQIECBAgAABAgQIECBAgAABAgQCAgaBwJNFJECAAAECBAgQIECAAAECBAgQIECAAAECBgEdIECAAAECBAgQIECAAAECBAgQIECAAAECAQGDQODJIhIgQIAAAQIECBAgQIAAAQIECBAgQIAAAYOADhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgYCAQSDwZBEJECBAgAABAgQIECBAgAABAgQIECBAgIBBQAcIECBAgAABAgQIECBAgAABAgQIECBAgEBAwCAQeLKIBAgQIECAAAECBAgQIECAAAECBAgQIEDAIKADBAgQIECAAAECBAgQIECAAAECBAgQIEAgIGAQCDxZRAIECBAgQIAAAQIECBAgQIAAAQIECBAgYBDQAQIECBAgQIAAAQIECBAgQIAAAQIECBAgEBAwCASeLCIBAgQIECBAgAABAgQIECBAgAABAgQIEDAI6AABAgQIECBAgAABAgQIECBAgAABAgQIEAgIGAQCTxaRAAECBAgQIECAAAECBAgQIECAAAECBAgYBHSAAAECBAgQIECAAAECBAgQIECAAAECBAgEBAwCgSeLSIAAAQIECBAgQIAAAQIECBAgQIAAAQIEDAI6QIAAAQIECBAgQIAAAQIECBAgQIAAAQIEAgIGgcCTRSRAgAABAgQIECBAgAABAgQIECBAgAABAgYBHSBAgAABAgQIECBAgAABAgQIECBAgAABAgEBg0DgySISIECAAAECBAgQIECAAAECBAgQIECAAAGDgA4QIECAAAECBAgQIECAAAECBAgQIECAAIGAgEEg8GQRCRAgQIAAAQIECBAgQIAAAQIECBAgQICAQUAHCBAgQIAAAQIECBAgQIAAAQIECBAgQIBAQMAgEHiyiAQIECBAgAABAgQIECBAgAABAgQIECBAwCCgAwQIECBAgAABAgQIECBAgAABAgQIECBAICBgEAg8WUQCBAgQIECAAAECBAgQIECAAAECBAgQIGAQ0AECBAgQIECAAAECBAgQIECAAAECBAgQIBAQMAgEniwiAQIECBAgQIAAAQIECBAgQIAAAQIECBAwCOgAAQIECBAgQIAAAQIECBAgQIAAAQIECBAICBgEAk8WkQABAgQIECBAgAABAgQIECBAgAABAgQIGAR0gAABAgQIECBAgAABAgQIECBAgAABAgQIBAQMAoEni0iAAAECBAgQIECAAAECBAgQIECAAAECBAwCOkCAAAECBAgQIECAAAECBAgQIECAAAECBAICBoHAk0UkQIAAAQIECBAgQIAAAQIECBAgQIAAAQIGAR0gQIAAAQIECBAgQIAAAQIECBAgQIAAAQIBAYNA4MkiEiBAgAABAgQIECBAgAABAgQIECBAgAABg4AOECBAgAABAgQIECBAgAABAgQIECBAgACBgIBBIPBkEQkQIECAAAECBAgQIECAAAECBAgQIECAgEFABwgQIECAAAECBAgQIECAAAECBAgQIECAQEDAIBB4sogECBAgQIAAAQIECBAgQIAAAQIECBAgQMAgoAMECBAgQIAAAQIECBAgQIAAAQIECBAgQCAgYBAIPFlEAgQIECBAgAABAgQIECBAgAABAgQIECBgENABAgQIECBAgAABAgQIECBAgAABAgQIECAQEDAIBJ4sIgECBAgQIECAAAECBAgQIECAAAECBAgQMAjoAAECBAgQIECAAAECBAgQIECAAAECBAgQCAgYBAJPFpEAAQIECBAgQIAAAQIECBAgQIAAAQIECBgEdIAAAQIECBAgQIAAAQIECBAgQIAAAQIECAQEDAKBJ4tIgAABAgQIECBAgAABAgQIECBAgAABAgQMAjpAgAABAgQIECBAgAABAgQIECBAgAABAgQCAgaBwJNFJECAAAECBAgQIECAAAECBAgQIECAAAECBgEdIECAAAECBAgQIECAAAECBAgQIECAAAECAQGDQODJIhIgQIAAAQIECBAgQIAAAQIECBAgQIAAAYOADhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgYCAQSDwZBEJECBAgAABAgQIECBAgAABAgQIECBAgIBBQAcIECBAgAABAgQIECBAgAABAgQIECBAgEBAwCAQeLKIBAgQIECAAAECBAgQIECAAAECBAgQIEDAIKADBAgQIECAAAECBAgQIECAAAECBAgQIEAgIGAQCDxZRAIECBAgQIAAAQIECBAgQIAAAQIECBAgYBDQAQIECBAgQIAAAQIECBAgQIAAAQIECBAgEBAwCASeLCIBAgQIECBAgAABAgQIECBAgAABAgQIEDAI6AABAgQIECBAgAABAgQIECBAgAABAgQIEAgIGAQCTxaRAAECBAgQIECAAAECBAgQIECAAAECBAgYBHSAAAECBAgQIECAAAECBAgQIECAAAECBAgEBAwCgSeLSIAAAQIECBAgQIAAAQIECBAgQIAAAQIEDAI6QIAAAQIECBAgQIAAAQIECBAgQIAAAQIEAgIGgcCTRSRAgAABAgQIECBAgAABAgQIECBAgAABAgYBHSBAgAABAgQIECBAgAABAgQIECBAgAABAgEBg0DgySISIECAAAECBAgQIECAAAECBAgQIECAAAGDgA4QIECAAAECBAgQIECAAAECBAgQIECAAIGAgEEg8GQRCRAgQIAAAQIECBAgQIAAAQIECBAgQICAQUAHCBAgQIAAAQIECBAgQIAAAQIECBAgQIBAQMAgEHiyiAQIECBAgAABAgQIECBAgAABAgQIECBAwCCgAwQIECBAgAABAgQIECBAgAABAgQIECBAICBgEAg8WUQCBAgQIECAAAECBAgQIECAAAECBAgQIGAQ0AECBAgQIECAAAECBAgQIECAAAECBAgQIBAQMAgEniwiAQIECBAgQIAAAQIECBAgQIAAAQIECBAwCOgAAQIECBAgQIAAAQIECBAgQIAAAQIECBAICBgEAk8WkQABAgQIECBAgAABAgQIECBAgAABAgQIGAR0gAABAgQIECBAgAABAgQIECBAgAABAgQIBAQMAoEni0iAAAECBAgQIECAAAECBAgQIECAAAECBAwCOkCAAAECBAgQIECAAAECBAgQIECAAAECBAICBoHAk0UkQIAAAQIECBAgQIAAAQIECBAgQIAAAQIGAR0gQIAAAQIECBAgQIAAAQIECBAgQIAAAQIBAYNA4MkiEiBAgAABAgQIECBAgAABAgQIECBAgAABg4AOECBAgAABAgQIECBAgAABAgQIECBAgACBgIBBIPBkEQkQIECAAAECBAgQIECAAAECBAgQIECAgEFABwgQIECAAAECBAgQIECAAAECBAgQIECAQEDAIBB4sogECBAgQIAAAQIECBAgQIAAAQIECBAgQMAgoAMECBAgQIAAAQIECBAgQIAAAQIECBAgQCAgYBAIPFlEAgQIECBAgAABAgQIECBAgAABAgQIECBgENABAgQIECBAgAABAgQIECBAgAABAgQIECAQEDAIBJ4sIgECBAgQIECAAAECBAgQIECAAAECBAgQMAjoAAECBAgQIECAAAECBAgQIECAAAECBAgQCAgYBAJPFpEAAQIECBAgQIAAAQIECBAgQIAAAQIECBgEdIAAAQIECBAgQIAAAQIECBAgQIAAAQIECAQEDAKBJ4tIgAABAgQIECBAgAABAgQIECBAgAABAgQMAjpAgAABAgQIECBAgAABAgQIECBAgAABAgQCAgaBwJNFJECAAAECBAgQIECAAAECBAgQIECAAAECBgEdIECAAAECBAgQIECAAAECBAgQIECAAAECAQGDQODJIhIgQIAAAQIECBAgQIAAAQIECBAgQIAAAYOADhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgYCAQSDwZBEJECBAgAABAgQIECBAgAABAgQIECBAgIBBQAcIECBAgAABAgQIECBAgAABAgQIECBAgEBAwCAQeLKIBAgQIECAAAECBAgQIECAAAECBAgQIEDAIKADBAgQIECAAAECBAgQIECAAAECBAgQIEAgIGAQCDxZRAIECBAgQIAAAQIECBAgQIAAAQIECBAgYBDQAQIECBAgQIAAAQIECBAgQIAAAQIECBAgEBAwCASeLCIBAgQIECBAgAABAgQIECBAgAABAgQIEDAI6AABAgQIECBAgAABAgQIECBAgAABAgQIEAgIGAQCTxaRAAECBAgQIECAAAECBAgQIECAAAECBAgYBHSAAAECBAgQIECAAAECBAgQIECAAAECBAgEBAwCgSeLSIAAAQIECBAgQIAAAQIECBAgQIAAAQIEDAI6QIAAAQIECBAgQIAAAQIECBAgQIAAAQIEAgIGgcCTRSRAgAABAgQIECBAgAABAgQIECBAgAABAgYBHSBAgAABAgQIECBAgAABAgQIECBAgAABAgEBg0DgySISIECAAAECBAgQIECAAAECBAgQIECAAAGDgA4QIECAAAECBAgQIECAAAECBAgQIECAAIGAgEEg8GQRCRAgQIAAAQIECBAgQIAAAQIECBAgQICAQUAHCBAgQIAAAQIECBAgQIAAAQIECBAgQIBAQMAgEHiyiAQIECBAgAABAgQIECBAgAABAgQIECBAwCCgAwQIECBAgAABAgQIECBAgAABAgQIECBAICBgEAg8WUQCBAgQIECAAAECBAgQIECAAAECBAgQIGAQ0AECBAgQIECAAAECBAgQIECAAAECBAgQIBAQMAgEniwiAQIECBAgQIAAAQIECBAgQIAAAQIECBAwCOgAAQIECBAgQIAAAQIECBAgQIAAAQIECBAICBgEAk8WkQABAgQIECBAgAABAgQIECBAgAABAgQIGAR0gAABAgQIECBAgAABAgQIECBAgAABAgQIBAQMAoEni0iAAAECBAgQIECAAAECBAgQIECAAAECBAwCOkCAAAECBAgQIECAAAECBAgQIECAAAECBAICBoHAk0UkQIAAAQIECBAgQIAAAQIECBAgQIAAAQIGAR0gQIAAAQIECBAgQIAAAQIECBAgQIAAAQIBAYNA4MkiEiBAgAABAgQIECBAgAABAgQIECBAgAABg4AOECBAgAABAgQIECBAgAABAgQIECBAgACBgIBBIPBkEQkQIECAAAECBAgQIECAAAECBAgQIECAgEFABwgQIECAAAECBAgQIECAAAECBAgQIECAQEDAIBB4sogECBAgQIAAAQIECBAgQIAAAQIECBAgQMAgoAMECBAgQIAAAQIECBAgQIAAAQIECBAgQCAgYBAIPFlEAgQIECBAgAABAgQIECBAgAABAgQIECBgENABAgQIECBAgAABAgQIECBAgAABAgQIECAQEDAIBJ4sIgECBAgQIECAAAECBAgQIECAAAECBAgQMAjoAAECBAgQIECAAAECBAgQIECAAAECBAgQCAgYBAJPFpEAAQIECBAgQIAAAQIECBAgQIAAAQIECBgEdIAAAQIECBAgQIAAAQIECBAgQIAAAQIECAQEDAKBJ4tIgAABAgQIECBAgAABAgQIECBAgAABAgQMAjpAgAABAgQIECBAgAABAgQIECBAgAABAgQCAgaBwJNFJECAAAECBAgQIECAAAECBAgQIECAAAECBgEdIECAAAECBAgQIECAAAECBAgQIECAAAECAQGDQODJIhIgQIAAAQIECBAgQIAAAQIECBAgQIAAAYOADhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgYCAQSDwZBEJECBAgAABAgQIECBAgAABAgQIECBAgIBBQAcIECBAgAABAgQIECBAgAABAgQIECBAgEBAwCAQeLKIBAgQIECAAAECBAgQIECAAAECBAgQIEDAIKADBAgQIECAAAECBAgQIECAAAECBAgQIEAgIGAQCDxZRAIECBAgQIAAAQIECBAgQIAAAQIECBAgYBDQAQIECBAgQIAAAQIECBAgQIAAAQIECBAgEBAwCASeLCIBAgQIECBAgAABAgQIECBAgAABAgQIEDAI6AABAgQIECBAgAABAgQIECBAgAABAgQIEAgIGAQCTxaRAAECBAgQIECAAAECBAgQIECAAAECBAgYBHSAAAECBAgQIECAAAECBAgQIECAAAECBAgEBAwCgSeLSIAAAQIECBAgQIAAAQIECBAgQIAAAQIEDAI6QIAAAQIECBAgQIAAAQIECBAgQIAAAQIEAgIGgcCTRSRAgAABAgQIECBAgAABAgQIECBAgAABAgYBHSBAgAABAgQIECBAgAABAgQIECBAgAABAgEBg0DgySISIECAAAECBAgQIECAAAECBAgQIECAAAGDgA4QIECAAAECBAgQIECAAAECBAgQIECAAIGAgEEg8GQRCRAgQIAAAQIECBAgQIAAAQIECBAgQICAQUAHCBAgQIAAAQIECBAgQIAAAQIECBAgQIBAQMAgEHiyiAQIECBAgAABAgQIECBAgAABAgQIECBAwCCgAwQIECBAgAABAgQIECBAgAABAgQIECBAICBgEAg8WUQCBAgQIECAAAECBAgQIECAAAECBAgQIGAQ0AECBAgQIECAAAECBAgQIECAAAECBAgQIBAQMAgEniwiAQIECBAgQIAAAQIECBAgQIAAAQIECBAwCOgAAQIECBAgQIAAAQIECBAgQIAAAQIECBAICBgEAk8WkQABAgQIECBAgAABAgQIECBAgAABAgQIGAR0gAABAgQIECBAgAABAgQIECBAgAABAgQIBAQMAoEni0iAAAECBAgQIECAAAECBAgQIECAAAECBAwCOkCAAAECBAgQIECAAAECBAgQIECAAAECBAICBoHAk0UkQIAAAQIECBAgQIAAAQIECBAgQIAAAQIGAR0gQIAAAQIECBAgQIAAAQIECBAgQIAAAQIBAYNA4MkiEiBAgAABAgQIECBAgAABAgQIECBAgAABg4AOECBAgAABAgQIECBAgAABAgQIECBAgACBgIBBIPBkEQkQIECAAAECBAgQIECAAAECBAgQIECAgEFABwgQIECAAAECBAgQIECAAAECBAgQIECAQEDAIBB4sogECBAgQIAAAQIECBAgQIAAAQIECBAgQMAgoAMECBAgQIAAAQIECBAgQIAAAQIECBAgQCAgYBAIPFlEAgQIECBAgAABAgQIECBAgAABAgQIECBgENABAgQIECBAgAABAgQIECBAgAABAgQIECAQEDAIBJ4sIgECBAgQIECAAAECBAgQIECAAAECBAgQMAjoAAECBAgQIECAAAECBAgQIECAAAECBAgQCAgYBAJPFpEAAQIECBAgQIAAAQIECBAgQIAAAQIECBgEdIAAAQIECBAgQIAAAQIECBAgQIAAAQIECAQEDAKBJ4tIgAABAgQIECBAgAABAgQIECBAgAABAgQMAjpAgAABAgQIECBAgAABAgQIECBAgAABAgQCAgaBwJNFJECAAAECBAgQIECAAAECBAgQIECAAAECBgEdIECAAAECBAgQIECAAAECBAgQIECAAAECAQGDQODJIhIgQIAAAQIECBAgQIAAAQIECBAgQIAAAYOADhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgYCAQSDwZBEJECBAgAABAgQIECBAgAABAgQIECBAgIBBQAcIECBAgAABAgQIECBAgAABAgQIECBAgEBAwCAQeLKIBAgQIECAAAECBAgQIECAAAECBAgQIEDAIKADBAgQIECAAAECBAgQIECAAAECBAgQIEAgIGAQCDxZRAIECBAgQIAAAQIECBAgQIAAAQIECBAgYBDQAQIECBAgQIAAAQIECBAgQIAAAQIECBAgEBAwCASeLCIBAgQIECBAgAABAgQIECBAgAABAgQIEDAI6AABAgQIECBAgAABAgQIECBAgAABAgQIEAgIGAQCTxaRAAECBAgQIECAAAECBAgQIECAAAECBAgYBHSAAAECBAgQIECAAAECBAgQIECAAAECBAgEBAwCgSeLSIAAAQIECBAgQIAAAQIECBAgQIAAAQIEDAI6QIAAAQIECBAgQIAAAQIECBAgQIAAAQIEAgIGgcCTRSRAgAABAgQIECBAgAABAgQIECBAgAABAgYBHSBAgAABAgQIECBAgAABAgQIECBAgAABAgEBg0DgySISIECAAAECBAgQIECAAAECBAgQIECAAAGDgA4QIECAAAECBAgQIECAAAECBAgQIECAAIGAgEEg8GQRCRAgQIAAAQIECBAgQIAAAQIECBAgQICAQUAHCBAgQIAAAQIECBAgQIAAAQIECBAgQIBAQMAgEHiyiAQIECBAgAABAgQIECBAgAABAgQIECBAwCCgAwQIECBAgAABAgQIECBAgAABAgQIECBAICBgEAg8WUQCBAgQIECAAAECBAgQIECAAAECBAgQIGAQ0AECBAgQIECAAAECBAgQIECAAAECBAgQIBAQMAgEniwiAQIECBAgQIAAAQIECBAgQIAAAQIECBAwCOgAAQIECBAgQIAAAQIECBAgQIAAAQIECBAICBgEAk8WkQABAgQIECBAgAABAgQIECBAgAABAgQIGAR0gAABAgQIECBAgAABAgQIECBAgAABAgQIBAQMAoEni0iAAAECBAgQIECAAAECBAgQIECAAAECBAwCOkCAAAECBAgQIECAAAECBAgQIECAAAECBAICBoHAk0UkQIAAAQIECBAgQIAAAQIECBAgQIAAAQIGAR0gQIAAAQIECBAgQIAAAQIECBAgQIAAAQIBAYNA4MkiEiBAgAABAgQIECBAgAABAgQIECBAgAABg4AOECBAgAABAgQIECBAgAABAgQIECBAgACBgIBBIPBkEQkQIECAAAECBAgQIECAAAECBAgQIECAgEFABwgQIECAAAECBAgQIECAAAECBAgQIECAQEDAIBB4sogECBAgQIAAAQIECBAgQIAAAQIECBAgQMAgoAMECBAgQIAAAQIECBAgQIAAAQIECBAgQCAgYBAIPFlEAgQIECBAgAABAgQIECBAgAABAgQIECBgENABAgQIECBAgAABAgQIECBAgAABAgQIECAQEDAIBJ4sIgECBAgQIECAAAECBAgQIECAAAECBAgQMAjoAAECBAgQIECAAAECBAgQIECAAAECBAgQCAgYBAJPFpEAAQIECBAgQIAAAQIECBAgQIAAAQIECBgEdIAAAQIECBAgQIAAAQIECBAgQIAAAQIECAQEDAKBJ4tIgAABAgQIECBAgAABAgQIECBAgAABAgQMAjpAgAABAgQIECBAgAABAgQIECBAgAABAgQCAgaBwJNFJECAAAECBAgQIECAAAECBAgQIECAAAECBgEdIECAAAECBAgQIECAAAECBAgQIECAAAECAQGDQODJIhIgQIAAAQIECBAgQIAAAQIECBAgQIAAAYOADhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgYCAQSDwZBEJECBAgAABAgQIECBAgAABAgQIECBAgIBBQAcIECBAgAABAgQIECBAgAABAgQIECBAgEBAwCAQeLKIBAgQIECAAAECBAgQIECAAAECBAgQIEDAIKADBAgQIECAAAECBAgQIECAAAECBAgQIEAgIGAQCDxZRAIECBAgQIAAAQIECBAgQIAAAQIECBAgYBDQAQIECBAgQIAAAQIECBAgQIAAAQIECBAgEBAwCASeLCIBAgQIECBAgAABAgQIECBAgAABAgQIEDAI6AABAgQIECBAgAABAgQIECBAgAABAgQIEAgIGAQCTxaRAAECBAgQIECAAAECBAgQIECAAAECBAgYBHSAAAECBAgQIECAAAECBAgQIECAAAECBAgEBAwCgSeLSIAAAQIECBAgQIAAAQIECBAgQIAAAQIEDAI6QIAAAQIECBAgQIAAAQIECBAgQIAAAQIEAgIGgcCTRSRAgAABAgQIECBAgAABAgQIECBAgAABAgYBHSBAgAABAgQIEAfGeXMAAAudSURBVCBAgAABAgQIECBAgAABAgEBg0DgySISIECAAAECBAgQIECAAAECBAgQIECAAAGDgA4QIECAAAECBAgQIECAAAECBAgQIECAAIGAgEEg8GQRCRAgQIAAAQIECBAgQIAAAQIECBAgQICAQUAHCBAgQIAAAQIECBAgQIAAAQIECBAgQIBAQMAgEHiyiAQIECBAgAABAgQIECBAgAABAgQIECBAwCCgAwQIECBAgAABAgQIECBAgAABAgQIECBAICBgEAg8WUQCBAgQIECAAAECBAgQIECAAAECBAgQIGAQ0AECBAgQIECAAAECBAgQIECAAAECBAgQIBAQMAgEniwiAQIECBAgQIAAAQIECBAgQIAAAQIECBAwCOgAAQIECBAgQIAAAQIECBAgQIAAAQIECBAICBgEAk8WkQABAgQIECBAgAABAgQIECBAgAABAgQIGAR0gAABAgQIECBAgAABAgQIECBAgAABAgQIBAQMAoEni0iAAAECBAgQIECAAAECBAgQIECAAAECBAwCOkCAAAECBAgQIECAAAECBAgQIECAAAECBAICBoHAk0UkQIAAAQIECBAgQIAAAQIECBAgQIAAAQIGAR0gQIAAAQIECBAgQIAAAQIECBAgQIAAAQIBAYNA4MkiEiBAgAABAgQIECBAgAABAgQIECBAgAABg4AOECBAgAABAgQIECBAgAABAgQIECBAgACBgIBBIPBkEQkQIECAAAECBAgQIECAAAECBAgQIECAgEFABwgQIECAAAECBAgQIECAAAECBAgQIECAQEDAIBB4sogECBAgQIAAAQIECBAgQIAAAQIECBAgQMAgoAMECBAgQIAAAQIECBAgQIAAAQIECBAgQCAgYBAIPFlEAgQIECBAgAABAgQIECBAgAABAgQIECBgENABAgQIECBAgAABAgQIECBAgAABAgQIECAQEDAIBJ4sIgECBAgQIECAAAECBAgQIECAAAECBAgQMAjoAAECBAgQIECAAAECBAgQIECAAAECBAgQCAgYBAJPFpEAAQIECBAgQIAAAQIECBAgQIAAAQIECBgEdIAAAQIECBAgQIAAAQIECBAgQIAAAQIECAQEDAKBJ4tIgAABAgQIECBAgAABAgQIECBAgAABAgQMAjpAgAABAgQIECBAgAABAgQIECBAgAABAgQCAgaBwJNFJECAAAECBAgQIECAAAECBAgQIECAAAECBgEdIECAAAECBAgQIECAAAECBAgQIECAAAECAQGDQODJIhIgQIAAAQIECBAgQIAAAQIECBAgQIAAAYOADhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgYCAQSDwZBEJECBAgAABAgQIECBAgAABAgQIECBAgIBBQAcIECBAgAABAgQIECBAgAABAgQIECBAgEBAwCAQeLKIBAgQIECAAAECBAgQIECAAAECBAgQIEDAIKADBAgQIECAAAECBAgQIECAAAECBAgQIEAgIGAQCDxZRAIECBAgQIAAAQIECBAgQIAAAQIECBAgYBDQAQIECBAgQIAAAQIECBAgQIAAAQIECBAgEBAwCASeLCIBAgQIECBAgAABAgQIECBAgAABAgQIEDAI6AABAgQIECBAgAABAgQIECBAgAABAgQIEAgIGAQCTxaRAAECBAgQIECAAAECBAgQIECAAAECBAgYBHSAAAECBAgQIECAAAECBAgQIECAAAECBAgEBAwCgSeLSIAAAQIECBAgQIAAAQIECBAgQIAAAQIEDAI6QIAAAQIECBAgQIAAAQIECBAgQIAAAQIEAgIGgcCTRSRAgAABAgQIECBAgAABAgQIECBAgAABAgYBHSBAgAABAgQIECBAgAABAgQIECBAgAABAgEBg0DgySISIECAAAECBAgQIECAAAECBAgQIECAAAGDgA4QIECAAAECBAgQIECAAAECBAgQIECAAIGAgEEg8GQRCRAgQIAAAQIECBAgQIAAAQIECBAgQICAQUAHCBAgQIAAAQIECBAgQIAAAQIECBAgQIBAQMAgEHiyiAQIECBAgAABAgQIECBAgAABAgQIECBAwCCgAwQIECBAgAABAgQIECBAgAABAgQIECBAICBgEAg8WUQCBAgQIECAAAECBAgQIECAAAECBAgQIGAQ0AECBAgQIECAAAECBAgQIECAAAECBAgQIBAQMAgEniwiAQIECBAgQIAAAQIECBAgQIAAAQIECBAwCOgAAQIECBAgQIAAAQIECBAgQIAAAQIECBAICBgEAk8WkQABAgQIECBAgAABAgQIECBAgAABAgQIGAR0gAABAgQIECBAgAABAgQIECBAgAABAgQIBAQMAoEni0iAAAECBAgQIECAAAECBAgQIECAAAECBAwCOkCAAAECBAgQIECAAAECBAgQIECAAAECBAICBoHAk0UkQIAAAQIECBAgQIAAAQIECBAgQIAAAQIGAR0gQIAAAQIECBAgQIAAAQIECBAgQIAAAQIBAYNA4MkiEiBAgAABAgQIECBAgAABAgQIECBAgAABg4AOECBAgAABAgQIECBAgAABAgQIECBAgACBgIBBIPBkEQkQIECAAAECBAgQIECAAAECBAgQIECAgEFABwgQIECAAAECBAgQIECAAAECBAgQIECAQEDAIBB4sogECBAgQIAAAQIECBAgQIAAAQIECBAgQMAgoAMECBAgQIAAAQIECBAgQIAAAQIECBAgQCAgYBAIPFlEAgQIECBAgAABAgQIECBAgAABAgQIECBgENABAgQIECBAgAABAgQIECBAgAABAgQIECAQEDAIBJ4sIgECBAgQIECAAAECBAgQIECAAAECBAgQMAjoAAECBAgQIECAAAECBAgQIECAAAECBAgQCAgYBAJPFpEAAQIECBAgQIAAAQIECBAgQIAAAQIECBgEdIAAAQIECBAgQIAAAQIECBAgQIAAAQIECAQEDAKBJ4tIgAABAgQIECBAgAABAgQIECBAgAABAgQMAjpAgAABAgQIECBAgAABAgQIECBAgAABAgQCAgaBwJNFJECAAAECBAgQIECAAAECBAgQIECAAAECBgEdIECAAAECBAgQIECAAAECBAgQIECAAAECAQGDQODJIhIgQIAAAQIECBAgQIAAAQIECBAgQIAAAYOADhAgQIAAAQIECBAgQIAAAQIECBAgQIAAgYCAQSDwZBEJECBAgAABAgQIECBAgAABAgQIECBAgIBBQAcIECBAgAABAgQIECBAgAABAgQIECBAgEBAwCAQeLKIBAgQIECAAAECBAgQIECAAAECBAgQIEDAIKADBAgQIECAAAECBAgQIECAAAECBAgQIEAgIGAQCDxZRAIECBAgQIAAAQIECBAgQIAAAQIECBAgYBDQAQIECBAgQIAAAQIECBAgQIAAAQIECBAgEBAwCASeLCIBAgQIECBAgAABAgQIECBAgAABAgQIEDAI6AABAgQIECBAgAABAgQIECBAgAABAgQIEAgIGAQCTxaRAAECBAgQIECAAAECBAgQIECAAAECBAgYBHSAAAECBAgQIECAAAECBAgQIECAAAECBAgEBAwCgSeLSIAAAQIECBAgQIAAAQIECBAgQIAAAQIEDAI6QIAAAQIECBAgQIAAAQIECBAgQIAAAQIEAgIGgcCTRSRAgAABAgQIECBAgAABAgQIECBAgAABAgYBHSBAgAABAgQIECBAgAABAgQIECBAgAABAgEBg0DgySISIECAAAECBAgQIECAAAECBAgQIECAAAGDgA4QIECAAAECBAgQIECAAAECBAgQIECAAIGAgEEg8GQRCRAgQIAAAQIECBAgQIAAAQIECBAgQICAQUAHCBAgQIAAAQIECBAgQIAAAQIECBAgQIBAQMAgEHiyiAQIECBAgAABAgQIECBAgAABAgQIECBAwCCgAwQIECBAgAABAgQIECBAgAABAgQIECBAICBwK2MBgoXeqwQAAAAASUVORK5CYII=';
            var sigData = $('#signature').jSignature('getData','default');
            if(emptySig == sigData) alert('same');
            $('#hiddenSigData').val(sigData);
            $('#textSigData').val(sigData);
            $("#imgSigData").attr('src',sigData);
            
        });
        

    })
    
    
</script>


