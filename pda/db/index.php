<?
session_start();

if ($_POST["submit_log_out"]) {
    session_unset();
    header("Location: http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?" . session_name() . "=" . session_id());
    exit;
}

$hostname    = "proust";
$db_user     = "phpbb";
$db_pass     = "y&sw09lEt";
$db_database = "wireless_db";

$db = mysql_connect("$hostname", "$db_user", "$db_pass") or die("could not connect!");
mysql_select_db($db_database) or die("could not select database!");

if ($_POST["submit_log_in"]) {
	$form_username = strtolower($_POST["username"]);
	$form_password = $_POST["password"];
	$form_db_id = $_POST["db_id"];
	if ($form_username && $form_password) {
		$result = mysql_query("SELECT password FROM auth WHERE username='$form_username'");
		if (mysql_num_rows($result) > 0) {
			if ($form_password == mysql_result($result, 0, "password")) {
				$_SESSION["username"] = $form_username;
				$_SESSION["db_id"] = $_POST["db_id"];
			}
			else {
				$error[] = "bad username or password!"; ### bad password
			}
		}
		else {
			$error[] = "bad username or password!"; ### bad username
		}
	}
	elseif ($form_username && !$form_password) {
		$error[] = "No password entered!";
	}
	elseif (!$form_username) {
		$error[] = "No username entered!";
	}
}

if ($_POST["submit_search"]) {
    $_SESSION["page"] = 0;
    $_SESSION["search"] = $_POST["search"];
    $_SESSION["search_field"] = $_POST["search_field"];
}
elseif ($_POST["submit_search_reset"]) {
    unset($_SESSION["search"]);
    unset($_SESSION["search_field"]);
}

if ($_GET["page"] || ($_GET["page"] == "0")) {
    $_SESSION["page"] = $_GET["page"];
}
elseif (!$_SESSION["page"]) {
    $_SESSION["page"] = 0;
}

if ($_GET["sort"]) {
    $_SESSION["sort"] = $_GET["sort"];
}
elseif (!$_SESSION["sort"]) {
    $_SESSION["sort"] = "CatalogNumber";
}

if ($_GET["sort_direction"]) {
    $_SESSION["sort_direction"] = $_GET["sort_direction"];
}
elseif (!$_SESSION["sort_direction"]) {
    $_SESSION["sort_direction"] = "ASC";
}

$edit_item_id = $_GET["edit_item_id"];
$view_item_id = $_GET["view_item_id"];
$edit_image_id = $_GET["edit_image_id"];
$view_image_id = $_GET["view_image_id"];
$del_item_id = $_GET["del_item_id"];
$image_id = $_GET["image_id"];

function nullify($value) {
    if ($value) {
        return "'$value'";
    }
    else {
        return "NULL";
    }
}
function get_id_name($table, $key) {
    global $db;
    if ($key) {
        $result = mysql_query("SELECT NAME FROM $table WHERE ID='$key'");
        if (mysql_num_rows($result) == 1) {
            return mysql_result($result, 0, "NAME");
        }
    }
    return false;
}
function show_row($header, $value) {
?>
    <tr>
        <td><?if ($header) {?><strong><?=$header?></strong><br><?}?><?=$value?></td>
    </tr>
<?
}
function get_select_year($name, $optselected) {
    $max_year = 2010;
    $select_html .= "<select name=\"$name\">";
    $select_html .= "<option value=\"\"></option>";
    for ($year=1995;$year<=$max_year;$year++) {
        $select_html .= "<option value=\"$year\"";
        if ($optselected == $year) {
            $select_html .= " selected";
        }
        $select_html .= ">$year</option>";
    }
    $select_html .= "</select>";
    return $select_html;
}
function get_select_month($name, $optselected) {
    $select_html .= "<select name=\"$name\">";
    $select_html .= "<option value=\"\"></option>";
    for ($month=1;$month<=12;$month++) {
        $select_html .= "<option value=\"$month\"";
        if ($optselected == $month) {
            $select_html .= " selected";
        }
        $select_html .= ">$month</option>";
    }
    $select_html .= "</select>";
    return $select_html;
}
function get_select_day($name, $optselected) {
    $select_html .= "<select name=\"$name\">";
    $select_html .= "<option value=\"\"></option>";
    for ($day=1;$day<=31;$day++) {
        $select_html .= "<option value=\"$day\"";
        if ($optselected == $day) {
            $select_html .= " selected";
        }
        $select_html .= ">$day</option>";
    }
    $select_html .= "</select>";
    return $select_html;
}
function get_select($name, $table, $optselected) {
    global $db;
    $result = mysql_query("SELECT ID, NAME FROM $table ORDER BY ID");

    if (mysql_num_rows($result) > 0) {
        $select_html .= "<select width=\"150\" name=\"$name\">";
        $select_html .= "<option value=\"\"></option>";
        while ($row = mysql_fetch_assoc($result)) {
            $select_html .= "<option value=\"{$row["ID"]}\"";
            if ($optselected == $row["ID"]) {
                $select_html .= " selected";
            }
            $select_html .= ">{$row["NAME"]}</option>";
        }
        $select_html .= "</select>";
    }
    else {
        return false;
    }
    return $select_html;
}
function get_textbox($name, $value) {
    return "<input name=\"$name\" type=\"text\" value=\"$value\" maxlength=\"100\">";
}
function get_textarea($name, $value) {
    return "<textarea name=\"$name\" rows=\"4\" cols=\"20\">$value</textarea>";
}
function get_file($name, $value) {
    return "<input name=\"$name\" type=\"file\">";
}
function get_checkbox($name, $value) {
    $select_html   .= "<input name=\"$name\" type=\"checkbox\" value=\"1\"";
    if ($value==1)
      $select_html .= " checked";

    $select_html   .= ">";

    return $select_html;

}

if ($image_id) {
    $result = mysql_query("SELECT ImageName, ImageData FROM images WHERE id='$image_id' AND db='{$_SESSION["db_id"]}'");
    if (mysql_num_rows($result) > 0) {
        $ImageName = mysql_result($result, 0, "ImageName");
        $ImageData = mysql_result($result, 0, "ImageData");
        $ImageSize = strlen($ImageData);
        header("Content-type: image/jpg");
        header("Content-disposition: filename=$imageName");
        header("Content-length: $ImageSize");
        print $ImageData;
        exit;
    }
}
elseif ($view_image_id || $edit_image_id) {
?>
<table width="480" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#dddddd">
        <td align="center"><strong>View Image</strong></td>
    </tr>
</table>
<table width="480" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center"><img src="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?image_id=<?=$view_image_id?><?=$edit_image_id?>"></td>
    </tr>
</table>
<table width="480" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#dddddd">
        <td align="center"><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?<?if ($edit_image_id) {?>edit_item_id=<?=$edit_image_id?><?}?><?if ($view_image_id) {?>view_item_id=<?=$view_image_id?><?}?>&<?=session_name()?>=<?=session_id()?>">return to '<?=$view_image_id ? "view" : "edit"?> item' page</a></td>
    </tr>
</table>
<?

}
elseif ($edit_item_id || ($edit_item_id == "-1")) {
    if ($_SESSION["db_id"] == "garbage") {
        $result = mysql_query("SELECT CatalogNumber, TotalWeight, DateRecorded, Source, Recorder, Count, FluidOunces, SolidOunces, Cost, WasteGms, Brand, Type, Material, Totals, TimeModified, DateModified, ID FROM {$_SESSION["db_id"]} WHERE ID='$edit_item_id'");
    }
    elseif ($_SESSION["db_id"] == "stonetools") {
        $result = mysql_query("SELECT CatalogNumber, UnitNumber, SizeCategory, LotCount, LotWeightGms, SpecimenType, MaterialType, CortexPercent, Description, DateRecorded, Recorder, GroupCode, TimeModified, DateModified, ID FROM {$_SESSION["db_id"]} WHERE ID='$edit_item_id'");
    }
    else {
        $result = mysql_query("SELECT * FROM {$_SESSION["db_id"]} WHERE ID='$edit_item_id'");
    }

    if ((mysql_num_rows($result) > 0)) {
        $result_image = mysql_query("SELECT ImageName FROM images WHERE id='$edit_item_id' AND db='{$_SESSION["db_id"]}'");
        if (mysql_num_rows($result_image) > 0) {
            $ImageName = mysql_result($result_image, 0, "ImageName");
        }
        else {
            $ImageName = "";
        }
        mysql_free_result($result_image);

        if ($_SESSION["db_id"] == "garbage") {
            $ImageData = "";
            $CatalogNumber = mysql_result($result, 0, "CatalogNumber");
            $TotalWeight = mysql_result($result, 0, "TotalWeight");
            $DateRecorded = mysql_result($result, 0, "DateRecorded");
            $Source = mysql_result($result, 0, "Source");
            $Recorder = mysql_result($result, 0, "Recorder");
            $Count = mysql_result($result, 0, "Count");
            $FluidOunces = mysql_result($result, 0, "FluidOunces");
            $SolidOunces = mysql_result($result, 0, "SolidOunces");
            $Cost = mysql_result($result, 0, "Cost");
            $WasteGms = mysql_result($result, 0, "WasteGms");
            $Brand = mysql_result($result, 0, "Brand");
            $Type = mysql_result($result, 0, "Type");
            $Material = mysql_result($result, 0, "Material");
            $Totals = mysql_result($result, 0, "Totals");
            $TimeModified = mysql_result($result, 0, "TimeModified");
            $DateModified = mysql_result($result, 0, "DateModified");
        }
        elseif ($_SESSION["db_id"] == "stonetools") {
            $ImageData = "";
            $CatalogNumber = mysql_result($result, 0, "CatalogNumber");
            $UnitNumber = mysql_result($result, 0, "UnitNumber");
            $SizeCategory = mysql_result($result, 0, "SizeCategory");
            $LotCount = mysql_result($result, 0, "LotCount");
            $LotWeightGms = mysql_result($result, 0, "LotWeightGms");
            $SpecimenType = mysql_result($result, 0, "SpecimenType");
            $MaterialType = mysql_result($result, 0, "MaterialType");
            $CortexPercent = mysql_result($result, 0, "CortexPercent");
            $Description = mysql_result($result, 0, "Description");
            $DateRecorded = mysql_result($result, 0, "DateRecorded");
            preg_match("/(\d+)\-(\d+)\-(\d+)/", $DateRecorded, $matches);
            $DateRecordedYear = $matches[1];
            $DateRecordedMonth = $matches[2];
            $DateRecordedDay = $matches[3];
            $Recorder = mysql_result($result, 0, "Recorder");
            $GroupCode = mysql_result($result, 0, "GroupCode");
            $TimeModified = mysql_result($result, 0, "TimeModified");
            $DateModified = mysql_result($result, 0, "DateModified");
        }
        else {
            $ImageData = "";
            $CatalogNumber = mysql_result($result, 0, "CatalogNumber");
            $UnitNumber = mysql_result($result, 0, "UnitNumber");
            $UnitLevel = mysql_result($result, 0, "UnitLevel");
            $Objective = mysql_result($result, 0, "Objective");
            $ArtifactType = mysql_result($result, 0, "ArtifactType");
            $DateRecovered = mysql_result($result, 0, "DateRecovered");
            preg_match("/(\d+)\-(\d+)\-(\d+)/", $DateRecovered, $matches);
            $DateRecoveredYear = $matches[1];
            $DateRecoveredMonth = $matches[2];
            $DateRecoveredDay = $matches[3];
            $FSNumber = mysql_result($result, 0, "FSNumber");
            $SiteName = mysql_result($result, 0, "SiteName");
            $SiteDesignation = mysql_result($result, 0, "SiteDesignation");
            $County = mysql_result($result, 0, "County");
            $State = mysql_result($result, 0, "State");
            $MunsellColor = mysql_result($result, 0, "MunsellColor");
            $InSituCoordinateN = mysql_result($result, 0, "InSituCoordinateN");
            $InSituCoordinateW = mysql_result($result, 0, "InSituCoordinateW");
            $InSituCoordinateD = mysql_result($result, 0, "InSituCoordinateD");
            $Description = mysql_result($result, 0, "Description");
            $Type = mysql_result($result, 0, "Type");
            $MaterialType = mysql_result($result, 0, "MaterialType");
            $SpeciesIdentification = mysql_result($result, 0, "SpeciesIdentification");
            $DimensionsL = mysql_result($result, 0, "DimensionsL");
            $DimensionsW = mysql_result($result, 0, "DimensionsW");
            $DimensionsD = mysql_result($result, 0, "DimensionsD");
            $WeightGms = mysql_result($result, 0, "WeightGms");
            $DateRecorded = mysql_result($result, 0, "DateRecorded");
            preg_match("/(\d+)\-(\d+)\-(\d+)/", $DateRecorded, $matches);
            $DateRecordedYear = $matches[1];
            $DateRecordedMonth = $matches[2];
            $DateRecordedDay = $matches[3];
            $Recorder = mysql_result($result, 0, "Recorder");
            $Excavator = mysql_result($result, 0, "Excavator");
            $TimeModified = mysql_result($result, 0, "TimeModified");
            $DateModified = mysql_result($result, 0, "DateModified");
            $UnitQuad = mysql_result($result, 0, "UnitQuad");
            $CrossReference = mysql_result($result, 0, "CrossReference");
            $Screen = mysql_result($result, 0, "Screen");
            
            //bla
            $PhotoSlide = mysql_result($result, 0, "PhotoSlide");
            $PhotoBlackWhite = mysql_result($result, 0, "PhotoBlackWhite");
            $PhotoDigital = mysql_result($result, 0, "PhotoDigital");
            $PhotoScan = mysql_result($result, 0, "PhotoScan");
            $PhotoXerox = mysql_result($result, 0, "PhotoXerox");
        }
    }
    elseif ($edit_item_id != "-1") {
        header("Location: http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?" . session_name() . "=" . session_id());
        exit;
    }
    else {
        $localtime_array = localtime();
        if ($_SESSION["db_id"] == "garbage") {
            $ImageName = "";
            $ImageData = "";
            $CatalogNumber = "";
            $TotalWeight = "";
            $DateRecorded = "";
            $DateRecordedYear = $localtime_array[5] + 1900;
            $DateRecordedMonth = $localtime_array[4] + 1;
            $DateRecordedDay = $localtime_array[3];
            $Source = "";
            $Recorder = "";
            $Count = "";
            $FluidOunces = "";
            $SolidOunces = "";
            $Cost = "";
            $WasteGms = "";
            $Brand = "";
            $Type = "";
            $Material = "";
            $Totals = "";
            $TimeModified = "";
            $DateModified = "";
        }
        elseif ($_SESSION["db_id"] == "stonetools") {
            $ImageData = "";
            $CatalogNumber = "";
            $UnitNumber = "";
            $SizeCategory = "";
            $LotCount = "";
            $LotWeightGms = "";
            $SpecimenType = "";
            $MaterialType = "";
            $CortexPercent = "";
            $Description = "";
            $DateRecorded = "";
            $DateRecordedYear = $localtime_array[5] + 1900;
            $DateRecordedMonth = $localtime_array[4] + 1;
            $DateRecordedDay = $localtime_array[3];
            $Recorder = "";
            $GroupCode = "";
            $TimeModified = "";
            $DateModified = "";
        }
        else {
            $ImageName = "";
            $ImageData = "";
            $CatalogNumber = "";
            $UnitNumber = "";
            $UnitLevel = "";
            $Objective = "";
            $ArtifactType = "";
            $DateRecovered = "";
            $DateRecoveredYear = $localtime_array[5] + 1900;
            $DateRecoveredMonth = $localtime_array[4] + 1;
            $DateRecoveredDay = $localtime_array[3];
            $FSNumber = "";
            $SiteName = "";
            $SiteDesignation = "";
            $County = "";
            $State = "";
            $MunsellColor = "";
            $InSituCoordinateN = "";
            $InSituCoordinateW = "";
            $InSituCoordinateD = "";
            $Description = "";
            $Type = "";
            $MaterialType = "";
            $SpeciesIdentification = "";
            $DimensionsL = "";
            $DimensionsW = "";
            $DimensionsD = "";
            $WeightGms = "";
            $DateRecorded = "";
            $DateRecordedYear = $localtime_array[5] + 1900;
            $DateRecordedMonth = $localtime_array[4] + 1;
            $DateRecordedDay = $localtime_array[3];
            $Recorder = "";
            $Excavator = "";
            $TimeModified = "";
            $DateModified = "";
            $UnitQuad = "";
            $CrossReference = "";
            $Screen = "";
            
            //cha ching
            $PhotoSlide = "";
            $PhotoBlackWhite = "";
            $PhotoDigital = "";
            $PhotoScan = "";
            $PhotoXerox = "";
        }
    }

    if ($_POST["submit_item"] && ($_POST["unique_token"] == $_SESSION["unique_token"])) {
        unset($_SESSION["unique_token"]);  ### prevents double form submission.  a unique key is stored in the session and the form.  when the form is submitted the first time, those keys match, and the script erases the key from the session.  if the form is submitted twice, the key in the session is already gone, so they no longer match, and no updates will be processed.
        $form_ImageNameTmp = $HTTP_POST_FILES['ImageName']['tmp_name'];
        $form_ImageName = $HTTP_POST_FILES['ImageName']['name'];
        $form_ImageSize = $HTTP_POST_FILES['ImageName']['size'];

        if ($_SESSION["db_id"] == "garbage" ) {
            $form_CatalogNumber = $_POST["CatalogNumber"];
            $form_TotalWeight = $_POST["TotalWeight"];
            $form_DateRecorded = "{$_POST["DateRecordedYear"]}-{$_POST["DateRecordedMonth"]}-{$_POST["DateRecordedDay"]}";
            $form_Source = $_POST["Source"];
            $form_Recorder = $_POST["Recorder"];
            $form_Count = $_POST["Count"];
            $form_FluidOunces = $_POST["FluidOunces"];
            $form_SolidOunces = $_POST["SolidOunces"];
            $form_Cost = $_POST["Cost"];
            $form_WasteGms = $_POST["WasteGms"];
            $form_Brand = $_POST["Brand"];
            $form_Type = $_POST["Type"];
            $form_Material = $_POST["Material"];
            $form_Totals = $_POST["Totals"];
            $form_TimeModified = $_POST["TimeModified"];
            $form_DateModified = $_POST["DateModified"];
        }
        elseif ($_SESSION["db_id"] == "stonetools" ) {
            $form_CatalogNumber = $_POST["CatalogNumber"];
            $form_UnitNumber = $_POST["UnitNumber"];
            $form_SizeCategory = $_POST["SizeCategory"];
            $form_LotCount = $_POST["LotCount"];
            $form_LotWeightGms = $_POST["LotWeightGms"];
            $form_SpecimenType = $_POST["SpecimenType"];
            $form_MaterialType = $_POST["MaterialType"];
            $form_CortexPercent = $_POST["CortexPercent"];
            $form_Description = $_POST["Description"];
            $form_DateRecorded = "{$_POST["DateRecordedYear"]}-{$_POST["DateRecordedMonth"]}-{$_POST["DateRecordedDay"]}";
            $form_Recorder = $_POST["Recorder"];
            $form_GroupCode = $_POST["GroupCode"];
            $form_TimeModified = $_POST["TimeModified"];
            $form_DateModified = $_POST["DateModified"];
        }
        else {
            $form_CatalogNumber = $_POST["CatalogNumber"];
            $form_UnitNumber = $_POST["UnitNumber"];
            $form_UnitLevel = $_POST["UnitLevel"];
            $form_Objective = $_POST["Objective"];
            $form_ArtifactType = $_POST["ArtifactType"];
            $form_DateRecovered = "{$_POST["DateRecoveredYear"]}-{$_POST["DateRecoveredMonth"]}-{$_POST["DateRecoveredDay"]}";
            $form_FSNumber = $_POST["FSNumber"];
            $form_SiteName = $_POST["SiteName"];
            $form_SiteDesignation = $_POST["SiteDesignation"];
            $form_County = $_POST["County"];
            $form_State = $_POST["State"];
            $form_MunsellColor = $_POST["MunsellColor"];
            $form_InSituCoordinateN = $_POST["InSituCoordinateN"];
            $form_InSituCoordinateW = $_POST["InSituCoordinateW"];
            $form_InSituCoordinateD = $_POST["InSituCoordinateD"];
            $form_Description = $_POST["Description"];
            $form_Type = $_POST["Type"];
            $form_MaterialType = $_POST["MaterialType"];
            $form_SpeciesIdentification = $_POST["SpeciesIdentification"];
            $form_DimensionsL = $_POST["DimensionsL"];
            $form_DimensionsW = $_POST["DimensionsW"];
            $form_DimensionsD = $_POST["DimensionsD"];
            $form_WeightGms = $_POST["WeightGms"];
            $form_DateRecorded = "{$_POST["DateRecordedYear"]}-{$_POST["DateRecordedMonth"]}-{$_POST["DateRecordedDay"]}";
            $form_Recorder = $_POST["Recorder"];
            $form_Excavator = $_POST["Excavator"];
            $form_TimeModified = $_POST["TimeModified"];
            $form_DateModified = $_POST["DateModified"];
            $form_UnitQuad = $_POST["UnitQuad"];
            $form_CrossReference = $_POST["CrossReference"];
            $form_Screen = $_POST["Screen"];


            // this too added
            $form_PhotoSlide = $_POST["PhotoSlide"];
            $form_PhotoBlackWhite = $_POST["PhotoBlackWhite"];
            $form_PhotoDigital = $_POST["PhotoDigital"];
            $form_PhotoScan = $_POST["PhotoScan"];
            $form_PhotoXerox = $_POST["PhotoXerox"];
        }
        ### validation and change detection here
        ### on error, $error = true;
        if ($form_CatalogNumber) {
            if ($form_CatalogNumber != $CatalogNumber) {
                if (preg_match("/^\d{1,5}\.\d{1,2}$/", $form_CatalogNumber)) {
                    $part_array = explode(".", $form_CatalogNumber);
                    $left_part = str_pad($part_array[0], 5, "0", STR_PAD_LEFT);
                    $right_part = str_pad($part_array[1], 2, "0", STR_PAD_LEFT);
                    $form_CatalogNumber = "$left_part.$right_part";
                    $item_sql_array[] = "CatalogNumber=" . nullify($form_CatalogNumber);
                }
                else {
                    $error[] = "Catalog # must be in xxxxx.yy format!";
                }
            }
        }
        elseif (!$form_CatalogNumber) {
            $error[] = "Catalog # is required!";
        }

        if ($_SESSION["db_id"] == "garbage") {
            if ($form_TotalWeight != $TotalWeight) {
                $item_sql_array[] = "TotalWeight=" . nullify($form_TotalWeight);
            }
            if ($form_DateRecorded != $DateRecorded) {
                $item_sql_array[] = "DateRecorded=" . nullify($form_DateRecorded);
            }
            if ($form_Source != $Source) {
                $item_sql_array[] = "Source=" . nullify($form_Source);
            }
            if ($form_Recorder != $Recorder) {
                $item_sql_array[] = "Recorder=" . nullify($form_Recorder);
            }
            if ($form_Count != $Count) {
                $item_sql_array[] = "Count=" . nullify($form_Count);
            }
            if ($form_FluidOunces != $FluidOunces) {
                $item_sql_array[] = "FluidOunces=" . nullify($form_FluidOunces);
            }
            if ($form_SolidOunces != $SolidOunces) {
                $item_sql_array[] = "SolidOunces=" . nullify($form_SolidOunces);
            }
            if ($form_Cost != $Cost) {
                $item_sql_array[] = "Cost=" . nullify($form_Cost);
            }
            if ($form_WasteGms != $WasteGms) {
                $item_sql_array[] = "WasteGms=" . nullify($form_WasteGms);
            }
            if ($form_Brand != $Brand) {
                $item_sql_array[] = "Brand=" . nullify($form_Brand);
            }
            if ($form_Type != $Type) {
                $item_sql_array[] = "Type=" . nullify($form_Type);
            }
            if ($form_Material != $Material) {
                $item_sql_array[] = "Material=" . nullify($form_Material);
            }
            if ($form_Totals != $Totals) {
                $item_sql_array[] = "Totals=" . nullify($form_Totals);
            }
            if (sizeof($item_sql_array) || $form_ImageNameTmp) { ### if anything requires updating
                $item_sql_array[] = "TimeModified=NOW()";
                $item_sql_array[] = "DateModified=NOW()";
            }
        }
        if ($_SESSION["db_id"] == "stonetools") {
            if ($form_CatalogNumber != $CatalogNumber) {
                $item_sql_array[] = "CatalogNumber=" . nullify($form_CatalogNumber);
            }
            if ($form_UnitNumber != $UnitNumber) {
                $item_sql_array[] = "UnitNumber=" . nullify($form_UnitNumber);
            }
            if ($form_SizeCategory != $SizeCategory) {
                $item_sql_array[] = "SizeCategory=" . nullify($form_SizeCategory);
            }
            if ($form_LotCount != $LotCount) {
                $item_sql_array[] = "LotCount=" . nullify($form_LotCount);
            }
            if ($form_LotWeightGms != $LotWeightGms) {
                $item_sql_array[] = "LotWeightGms=" . nullify($form_LotWeightGms);
            }
            if ($form_SpecimenType != $SpecimenType) {
                $item_sql_array[] = "SpecimenType=" . nullify($form_SpecimenType);
            }
            if ($form_MaterialType != $MaterialType) {
                $item_sql_array[] = "MaterialType=" . nullify($form_MaterialType);
            }
            if ($form_CortexPercent != $CortexPercent) {
                $item_sql_array[] = "CortexPercent=" . nullify($form_CortexPercent);
            }
            if ($form_Description != $Description) {
                $item_sql_array[] = "Description=" . nullify($form_Description);
            }
            if ($form_DateRecorded != $DateRecorded) {
                $item_sql_array[] = "DateRecorded=" . nullify($form_DateRecorded);
            }
            if ($form_GroupCode != $GroupCode) {
                $item_sql_array[] = "GroupCode=" . nullify($form_GroupCode);
            }
            if ($form_Recorder != $Recorder) {
                $item_sql_array[] = "Recorder=" . nullify($form_Recorder);
            }
            if (sizeof($item_sql_array) || $form_ImageNameTmp) { ### if anything requires updating
                $item_sql_array[] = "TimeModified=NOW()";
                $item_sql_array[] = "DateModified=NOW()";
            }
        }
        else {
            if ($form_UnitNumber != $UnitNumber) {
                $item_sql_array[] = "UnitNumber=" . nullify($form_UnitNumber);
            }
            if ($form_UnitLevel != $UnitLevel) {
                $item_sql_array[] = "UnitLevel=" . nullify($form_UnitLevel);
            }
            if ($form_Objective != $Objective) {
                $item_sql_array[] = "Objective=" . nullify($form_Objective);
            }
            if ($form_ArtifactType != $ArtifactType) {
                $item_sql_array[] = "ArtifactType=" . nullify($form_ArtifactType);
            }
            if ($form_DateRecovered != $DateRecovered) {
                $item_sql_array[] = "DateRecovered=" . nullify($form_DateRecovered);
            }
            if ($form_FSNumber != $FSNumber) {
                $item_sql_array[] = "FSNumber=" . nullify($form_FSNumber);
            }
            if ($form_SiteName != $SiteName) {
                $item_sql_array[] = "SiteName=" . nullify($form_SiteName);
            }
            if ($form_SiteDesignation != $SiteDesignation) {
                $item_sql_array[] = "SiteDesignation=" . nullify($form_SiteDesignation);
            }
            if ($form_County != $County) {
                $item_sql_array[] = "County=" . nullify($form_County);
            }
            if ($form_State != $State) {
                $item_sql_array[] = "State=" . nullify($form_State);
            }
            if ($form_MunsellColor != $MunsellColor) {
                $item_sql_array[] = "MunsellColor=" . nullify($form_MunsellColor);
            }
            if ($form_InSituCoordinateN != $InSituCoordinateN) {
                $item_sql_array[] = "InSituCoordinateN=" . nullify($form_InSituCoordinateN);
            }
            if ($form_InSituCoordinateW != $InSituCoordinateW) {
                $item_sql_array[] = "InSituCoordinateW=" . nullify($form_InSituCoordinateW);
            }
            if ($form_InSituCoordinateD != $InSituCoordinateD) {
                $item_sql_array[] = "InSituCoordinateD=" . nullify($form_InSituCoordinateD);
            }
            if ($form_Description != $Description) {
                $item_sql_array[] = "Description=" . nullify($form_Description);
            }
            if ($form_Type != $Type) {
                $item_sql_array[] = "Type=" . nullify($form_Type);
            }
            if ($form_MaterialType != $MaterialType) {
                $item_sql_array[] = "MaterialType=" . nullify($form_MaterialType);
            }
            if ($form_SpeciesIdentification != $SpeciesIdentification) {
                $item_sql_array[] = "SpeciesIdentification=" . nullify($form_SpeciesIdentification);
            }
            if ($form_DimensionsL != $DimensionsL) {
                $item_sql_array[] = "DimensionsL=" . nullify($form_DimensionsL);
            }
            if ($form_DimensionsW != $DimensionsW) {
                $item_sql_array[] = "DimensionsW=" . nullify($form_DimensionsW);
            }
            if ($form_DimensionsD != $DimensionsD) {
                $item_sql_array[] = "DimensionsD=" . nullify($form_DimensionsD);
            }
            if ($form_WeightGms != $WeightGms) {
                $item_sql_array[] = "WeightGms=" . nullify($form_WeightGms);
            }
            if ($form_DateRecorded != $DateRecorded) {
                $item_sql_array[] = "DateRecorded=" . nullify($form_DateRecorded);
            }
            if ($form_Recorder != $Recorder) {
                $item_sql_array[] = "Recorder=" . nullify($form_Recorder);
            }
            if ($form_Excavator != $Excavator) {
                $item_sql_array[] = "Excavator=" . nullify($form_Excavator);
            }
            #if ($form_TimeModified != $TimeModified) {
                #$item_sql_array[] = "TimeModified=" . nullify($form_TimeModified);
            #}
            #if ($form_DateModified != $DateModified) {
                #$item_sql_array[] = "DateModified=" . nullify($form_DateModified);
            #}
            if ($form_UnitQuad != $UnitQuad) {
                $item_sql_array[] = "UnitQuad=" . nullify($form_UnitQuad);
            }
            if ($form_CrossReference != $CrossReference) {
                $item_sql_array[] = "CrossReference=" . nullify($form_CrossReference);
            }
            if ($form_Screen != $Screen) {
                $item_sql_array[] = "Screen=" . nullify($form_Screen);
            }
            if ($form_Recorder != $Recorder) {
                $item_sql_array[] = "Recorder=" . nullify($form_Recorder);
            }
            if (sizeof($item_sql_array) || $form_ImageNameTmp) { ### if anything requires updating
                $item_sql_array[] = "TimeModified=NOW()";
                $item_sql_array[] = "DateModified=NOW()";
            }
            //bla bla bla chinaa
            if ($form_PhotoSlide != $PhotoSlide) {
                $item_sql_array[] = "PhotoSlide=" . nullify($form_PhotoSlide);
            }
            if ($form_PhotoBlackWhite != $PhotoBlackWhite) {
                $item_sql_array[] = "PhotoBlackWhite=" . nullify($form_PhotoBlackWhite);
            }
            if ($form_PhotoDigital != $PhotoDigital) {
                $item_sql_array[] = "PhotoDigital=" . nullify($form_PhotoDigital);
            }
            if ($form_PhotoScan != $PhotoScan) {
                $item_sql_array[] = "PhotoScan=" . nullify($form_PhotoScan);
            }
            if ($form_PhotoXerox != $PhotoXerox) {
                $item_sql_array[] = "PhotoXerox=" . nullify($form_PhotoXerox);
            }
        }

        if (sizeof($item_sql_array)) { ### if anything requires updating
            if (!sizeof($error)) { ### if no error in validation, do db inserts/updates
                $item_sql = implode(", ", $item_sql_array);
                if ($edit_item_id == "-1") { ### if this is a new item, make a blank row in the db and get its id, if its not new, an id will already exist
                    ### insert new item id
                    mysql_query("INSERT INTO {$_SESSION["db_id"]} (DateRecorded) VALUES (NOW())");
                    $edit_item_id = mysql_insert_id();
                    if (!$form_Recorder) {
                        mysql_query("UPDATE {$_SESSION["db_id"]} SET Recorder='{$_SESSION["username"]}' WHERE ID='$edit_item_id'");
                    }
                }

                if ($form_ImageNameTmp && $form_ImageName) { ### if an image was uploaded
                    if (($form_ImageSize == 0) || ($form_ImageSize > 250000)) {
                        $error[] = "Image failed to upload!<br>Might be too large (250k max)";
                    }
                    else {
                        $form_ImageData = addslashes(fread(fopen($form_ImageNameTmp, "rb"), filesize($form_ImageNameTmp)));
                        if ($ImageName) { ## image already in db
                            mysql_query("UPDATE images SET ImageName='$form_ImageName', ImageData='$form_ImageData' WHERE id='$edit_item_id' AND db='{$_SESSION["db_id"]}'");
                        }
                        else {
                            mysql_query("INSERT INTO images (id, db, ImageName, ImageData) VALUES ('$edit_item_id', '{$_SESSION["db_id"]}', '$form_ImageName', '$form_ImageData')");
                        }
                        $_SESSION["success"][] = "Image updated!";
                    }
                }

                if (!sizeof($error)) {
                    ### update the db with form fields
                    mysql_query("UPDATE {$_SESSION["db_id"]} SET $item_sql WHERE ID='$edit_item_id'");
                    $_SESSION["success"][] = "Item #$form_CatalogNumber updated!";
                    header("Location: http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?view_item_id=$edit_item_id&" . session_name() . "=" . session_id());
                    exit;
                }
            }
        }
    }

    $_SESSION["unique_token"] = substr(ereg_replace("[^A-Za-z]", "", crypt(time())) . ereg_replace("[^A-Za-z]", "", crypt(time())) . ereg_replace("[^A-Za-z]", "", crypt(time())), 0, 8);
?>
<form method="post" enctype="multipart/form-data" action="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?edit_item_id=<?=$edit_item_id?>&<?=session_name()?>=<?=session_id()?>">
<input type="hidden" name="MAX_FILE_SIZE" value="250000"> 
<input type="hidden" name="unique_token" value="<?=$_SESSION["unique_token"]?>">
<table width="480" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#dddddd">
        <td align="center"><strong>Edit Item</strong></td>
    </tr>
<?
    if (sizeof($error)) {
?>
    <tr bgcolor="#eeeeee">
        <td align="left"><font color="red"><?=implode("<br>", $error)?></font></td>
    </tr>
<?
    }

    if ($_SESSION["db_id"] == "garbage") {
        show_row("Catalog Number", get_textbox("CatalogNumber", $CatalogNumber));
        show_row("Brand", get_textbox("Brand", $Brand));
        if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?edit_image_id=$edit_item_id&" . session_name() . "=" . session_id() . "\">click here</a>");    
        show_row(($ImageName ? "Replace " : "Upload ") . "Image", get_file("ImageName", $ImageName));
        show_row("Total Weight", get_textbox("TotalWeight", $TotalWeight));
        show_row("Source", get_textbox("Source", $Source));
        show_row("Count", get_textbox("Count", $Count));
        show_row("Fluid Ounces", get_textbox("FluidOunces", $FluidOunces));
        show_row("Solid Ounces", get_textbox("SolidOunces", $SolidOunces));
        show_row("Cost", get_textbox("Cost", $Cost));
        show_row("Waste gms.", get_textbox("WasteGms", $WasteGms));
        show_row("Type", get_textbox("Type", $Type));
        show_row("Material", get_textbox("Material", $Material));
        show_row("Totals", get_textbox("Totals", $Totals));
        show_row("Recorder", get_textbox("Recorder", $Recorder));
        show_row("Collection Date", get_textbox("DateRecorded", $DateRecorded));
        if ($DateModified) show_row("Date Modified", $DateModified);
        if ($TimeModified) show_row("Time Modified", $TimeModified);
    }
    elseif ($_SESSION["db_id"] == "stonetools") {
        show_row("Catalog Number", get_textbox("CatalogNumber", $CatalogNumber));
        show_row("Description", get_textarea("Description", $Description));
        if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?edit_image_id=$edit_item_id&" . session_name() . "=" . session_id() . "\">click here</a>");
        show_row(($ImageName ? "Replace " : "Upload ") . "Image", get_file("ImageName", $ImageName));
        show_row("Unit Number", get_textbox("UnitNumber", $UnitNumber));
        show_row("Size Category", get_select("SizeCategory", "sizecategorylist", $SizeCategory));
        show_row("Lot Count", get_textbox("LotCount", $LotCount));
        show_row("Lot Weight Gms", get_textbox("LotWeightGms", $LotWeightGms));
        show_row("Specimen Type", get_select("SpecimenType", "specimentypelist", $SpecimenType));
        show_row("Material Type", get_select("MaterialType", "materialtypelist2", $MaterialType));
        show_row("Cortex Percent", get_select("CortexPercent", "cortexpercentlist", $CortexPercent));
        show_row("Group Code", get_select("GroupCode", "groupcodelist", $GroupCode));
        show_row("Recorder", get_textbox("Recorder", $Recorder));
        show_row("Collection Date", get_select_year("DateRecordedYear", $DateRecordedYear).get_select_month("DateRecordedMonth", $DateRecordedMonth).get_select_day("DateRecordedDay", $DateRecordedDay));
        if ($DateModified) show_row("Date Modified", $DateModified);
        if ($TimeModified) show_row("Time Modified", $TimeModified);
    }
    else {
        show_row("Catalog Number", get_textbox("CatalogNumber", $CatalogNumber));
        show_row("FS Number", get_select("FSNumber", "fslist", $FSNumber));
        show_row("Site Designation", get_select("SiteDesignation", "sitedesignationlist", $SiteDesignation));
        show_row("Site Name", get_select("SiteName", "sitenamelist", $SiteName));
        show_row("County", get_select("County", "countylist", $County));
        show_row("State", get_select("State", "statelist",  $State));
        show_row("Unit Number", get_textbox("UnitNumber", $UnitNumber));
        show_row("Unit Level", get_select("UnitLevel", "unitlevellist", $UnitLevel));
        show_row("Objective", get_select("Objective", "objectivelist", $Objective));
        show_row("Unit Quad", get_select("UnitQuad", "unitquadlist", $UnitQuad));
        show_row("Screen", get_checkbox("Screen", $Screen));
        show_row("In Situ Coordinate N", get_textbox("InSituCoordinateN", $InSituCoordinateN));
        show_row("In Situ Coordinate W", get_textbox("InSituCoordinateW", $InSituCoordinateW));
        show_row("In Situ Coordinate D", get_textbox("InSituCoordinateD", $InSituCoordinateD));
        show_row("Munsell Color", get_textbox("MunsellColor", $MunsellColor));
        show_row("Artifact Type", get_select("ArtifactType", "artifacttypelist", $ArtifactType));
        show_row("Species Identification", get_textbox("SpeciesIdentification", $SpeciesIdentification));
        show_row("Cross Reference", get_textbox("CrossReference", $CrossReference));
        show_row("Material Type", get_select("MaterialType", "materialtypelist", $MaterialType));
        show_row("Type", get_textbox("Type", $Type));
        show_row("Dimensions L", get_textbox("DimensionsL", $DimensionsL));
        show_row("Dimensions W", get_textbox("DimensionsW", $DimensionsW));
        show_row("Dimensions D", get_textbox("DimensionsD", $DimensionsD));
        show_row("Weight gms.", get_textbox("WeightGms", $WeightGms));
        show_row("Description", get_textarea("Description", $Description));
        if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?edit_image_id=$edit_item_id&" . session_name() . "=" . session_id() . "\">click here</a>");
        show_row(($ImageName ? "Replace " : "Upload ") . "Image", get_file("ImageName", $ImageName));
        show_row("Date Recovered", get_select_year("DateRecoveredYear", $DateRecoveredYear).get_select_month("DateRecoveredMonth", $DateRecoveredMonth).get_select_day("DateRecoveredDay", $DateRecoveredDay));
        show_row("Excavator", get_textbox("Excavator", $Excavator));
        show_row("Recorder", get_textbox("Recorder", $Recorder));
        show_row("Date Recorded", get_select_year("DateRecordedYear", $DateRecordedYear).get_select_month("DateRecordedMonth", $DateRecordedMonth).get_select_day("DateRecordedDay", $DateRecordedDay));
        
        //fix this: make close to eachother
        show_row("PhotoSlide", get_checkbox("PhotoSlide", $PhotoSlide));
        show_row("PhotoBlackWhite", get_checkbox("PhotoBlackWhite", $PhotoBlackWhite));
        show_row("PhotoDigital", get_checkbox("PhotoDigital", $PhotoDigital));
        show_row("PhotoScan", get_checkbox("PhotoScan", $PhotoScan));
        show_row("PhotoXerox", get_checkbox("PhotoXerox", $PhotoXerox));
        
        if ($DateModified) show_row("Date Modified", $DateModified);
        if ($TimeModified) show_row("Time Modified", $TimeModified);
    }
?>
    <tr bgcolor="#eeeeee">
        <td align="left"><table width="100%" cellpadding="0" cellspacing="0"><tr><td align="left"><input type="reset" name="reset" value="Reset Form"></td><td align="right"><input type="submit" name="submit_item" value="Update Item"></td></tr></table></td>
    </tr>
    <tr bgcolor="#dddddd">
        <td align="center"><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?view_item_id=<?=$edit_item_id?>&<?=session_name()?>=<?=session_id()?>">view this item</a></td>
    </tr>
    <tr bgcolor="#eeeeee">
        <td align="center"><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?<?=session_name()?>=<?=session_id()?>">return to main page</a></td>
    </tr>
</table>
</form>
<?
}
elseif ($view_item_id) {

    $result = mysql_query("SELECT * FROM {$_SESSION["db_id"]} WHERE ID='$view_item_id'");

    if (mysql_num_rows($result) > 0) {
        $result_image = mysql_query("SELECT ImageName, ImageData FROM images WHERE id='$view_item_id' AND db='{$_SESSION["db_id"]}'");

        if (mysql_num_rows($result_image) > 0) {
            $ImageName = mysql_result($result_image, 0, "ImageName");
            $ImageData = mysql_result($result_image, 0, "ImageData");
        }
        mysql_free_result($result_image);

        if ($_SESSION["db_id"] == "garbage" ) {
            $CatalogNumber = mysql_result($result, 0, "CatalogNumber");
            $TotalWeight = mysql_result($result, 0, "TotalWeight");
            $DateRecorded = mysql_result($result, 0, "DateRecorded");
            $Source = mysql_result($result, 0, "Source");
            $Recorder = mysql_result($result, 0, "Recorder");
            $Count = mysql_result($result, 0, "Count");
            $FluidOunces = mysql_result($result, 0, "FluidOunces");
            $SolidOunces = mysql_result($result, 0, "SolidOunces");
            $Cost = mysql_result($result, 0, "Cost");
            $WasteGms = mysql_result($result, 0, "WasteGms");
            $Brand = mysql_result($result, 0, "Brand");
            $Type = mysql_result($result, 0, "Type");
            $Material = mysql_result($result, 0, "Material");
            $Totals = mysql_result($result, 0, "Totals");
            $TimeModified = mysql_result($result, 0, "TimeModified");
            $DateModified = mysql_result($result, 0, "DateModified");
        }
        elseif ($_SESSION["db_id"] == "stonetools") {
            $ImageData = "";
            $CatalogNumber = mysql_result($result, 0, "CatalogNumber");
            $UnitNumber = mysql_result($result, 0, "UnitNumber");
            $SizeCategory = mysql_result($result, 0, "SizeCategory");
            $LotCount = mysql_result($result, 0, "LotCount");
            $LotWeightGms = mysql_result($result, 0, "LotWeightGms");
            $SpecimenType = mysql_result($result, 0, "SpecimenType");
            $MaterialType = mysql_result($result, 0, "MaterialType");
            $CortexPercent = mysql_result($result, 0, "CortexPercent");
            $Description = mysql_result($result, 0, "Description");
            $DateRecorded = mysql_result($result, 0, "DateRecorded");
            preg_match("/(\d+)\-(\d+)\-(\d+)/", $DateRecorded, $matches);
            $DateRecordedYear = $matches[1];
            $DateRecordedMonth = $matches[2];
            $DateRecordedDay = $matches[3];
            $Recorder = mysql_result($result, 0, "Recorder");
            $GroupCode = mysql_result($result, 0, "GroupCode");
            $TimeModified = mysql_result($result, 0, "TimeModified");
            $DateModified = mysql_result($result, 0, "DateModified");
        }
        else {
            $CatalogNumber = mysql_result($result, 0, "CatalogNumber");
            $UnitNumber = mysql_result($result, 0, "UnitNumber");
            $UnitLevel = mysql_result($result, 0, "UnitLevel");
            $Objective = mysql_result($result, 0, "Objective");
            $ArtifactType = mysql_result($result, 0, "ArtifactType");
            $DateRecovered = mysql_result($result, 0, "DateRecovered");
            $FSNumber = mysql_result($result, 0, "FSNumber");
            $SiteName = mysql_result($result, 0, "SiteName");
            $SiteDesignation = mysql_result($result, 0, "SiteDesignation");
            $County = mysql_result($result, 0, "County");
            $State = mysql_result($result, 0, "State");
            $MunsellColor = mysql_result($result, 0, "MunsellColor");
            $InSituCoordinateN = mysql_result($result, 0, "InSituCoordinateN");
            $InSituCoordinateW = mysql_result($result, 0, "InSituCoordinateW");
            $InSituCoordinateD = mysql_result($result, 0, "InSituCoordinateD");
            $Description = mysql_result($result, 0, "Description");
            $Type = mysql_result($result, 0, "Type");
            $MaterialType = mysql_result($result, 0, "MaterialType");
            $SpeciesIdentification = mysql_result($result, 0, "SpeciesIdentification");
            $DimensionsL = mysql_result($result, 0, "DimensionsL");
            $DimensionsW = mysql_result($result, 0, "DimensionsW");
            $DimensionsD = mysql_result($result, 0, "DimensionsD");
            $WeightGms = mysql_result($result, 0, "WeightGms");
            $DateRecorded = mysql_result($result, 0, "DateRecorded");
            $Recorder = mysql_result($result, 0, "Recorder");
            $Excavator = mysql_result($result, 0, "Excavator");
            $TimeModified = mysql_result($result, 0, "TimeModified");
            $DateModified = mysql_result($result, 0, "DateModified");
            $UnitQuad = mysql_result($result, 0, "UnitQuad");
            $CrossReference = mysql_result($result, 0, "CrossReference");
            $Screen = mysql_result($result, 0, "Screen");
            
            $PhotoSlide = mysql_result($result, 0, "PhotoSlide");
            $PhotoBlackWhite = mysql_result($result, 0, "PhotoBlackWhite");
            $PhotoDigital = mysql_result($result, 0, "PhotoDigital");
            $PhotoScan = mysql_result($result, 0, "PhotoScan");
            $PhotoXerox = mysql_result($result, 0, "PhotoXerox");
        }
?>
<table width="480" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#dddddd">
        <td align="center"><strong>View Item</strong></td>
    </tr>
<?
        if (sizeof($_SESSION["success"])) {
?>
    <tr bgcolor="#eeeeee">
        <td align="center"><font color="green"><?=implode("<br>", $_SESSION["success"])?></font></td>
    </tr>
<?
            unset($_SESSION["success"]);
        }

        if ($_SESSION["db_id"] == "garbage") {
            if ($CatalogNumber) show_row("Catalog Number", $CatalogNumber);
            if ($Brand) show_row("Brand", $Brand);
            if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?view_image_id=$view_item_id&" . session_name() . "=" . session_id() . "\">click here</a>");
            if ($TotalWeight) show_row("Total Weight", $TotalWeight);
            if ($Source) show_row("Source", $Source);
            if ($Count) show_row("Count", $Count);
            if ($FluidOunces) show_row("Fluid Ounces", $FluidOunces);
            if ($SolidOunces) show_row("Solid Ounces", $SolidOunces);
            if ($Cost) show_row("Cost", $Cost);
            if ($WasteGms) show_row("WasteGms", $WasteGms);
            if ($Type) show_row("Type", $Type);
            if ($Material) show_row("Material", $Material);
            if ($Totals) show_row("Totals", $Totals);
            if ($Recorder) show_row("Recorder", $Recorder);
            if ($DateRecorded) show_row("Collection Date", $DateRecorded);
            if ($DateModified) show_row("Date Modified", $DateModified);
            if ($TimeModified) show_row("Time Modified", $TimeModified);
        }
        elseif ($_SESSION["db_id"] == "stonetools") {
            if ($CatalogNumber) show_row("Catalog Number", $CatalogNumber);
            if ($Description) show_row("Description", $Description);
            if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?view_image_id=$view_item_id&" . session_name() . "=" . session_id() . "\">click here</a>");
            if ($UnitNumber) show_row("Unit Number", $UnitNumber);
            if ($SizeCategory) show_row("Size Category", get_id_name("sizecategorylist", $SizeCategory));
            if ($LotCount) show_row("Lot Count", $LotCount);
            if ($LotWeightGms) show_row("Lot Weight Gms", $LotWeightGms);
            if ($SpecimenType) show_row("Specimen Type", get_id_name("specimentypelist", $SpecimenType));
            if ($MaterialType) show_row("Material Type", get_id_name("materialtypelist2", $MaterialType));
            if ($CortexPercent) show_row("Cortex Percent", get_id_name("cortexpercentlist", $CortexPercent));
            if ($GroupCode) show_row("Group Code", get_id_name("groupcodelist", $GroupCode));
            if ($Recorder) show_row("Recorder", $Recorder);
            if ($DateRecorded) show_row("Collection Date", $DateRecorded);
            if ($DateModified) show_row("Date Modified", $DateModified);
            if ($TimeModified) show_row("Time Modified", $TimeModified);
        }
        else {
            if ($CatalogNumber) show_row("Catalog Number", $CatalogNumber);
            if ($FSNumber) show_row("FS Number", get_id_name("fslist", $FSNumber));
            if ($SiteDesignation) show_row("Site Designation", get_id_name("sitedesignationlist", $SiteDesignation));
            if ($SiteName) show_row("Site Name", get_id_name("sitenamelist", $SiteName));
            if ($County) show_row("County", get_id_name("countylist", $County));
            if ($State) show_row("State", get_id_name("statelist",  $State));
            if ($UnitNumber) show_row("Unit Number", $UnitNumber);
            if ($UnitLevel) show_row("Unit Level", get_id_name("unitlevellist", $UnitLevel));
            if ($Objective) show_row("Objective", get_id_name("objectivelist", $Objective));
            if ($UnitQuad) show_row("Unit Quad", get_id_name("unitquadlist", $UnitQuad));
            if ($Screen==1) show_row("Screen", "Lot");
            if ($InSituCoordinateN) show_row("In Situ Coordinate N", $InSituCoordinateN);
            if ($InSituCoordinateW) show_row("In Situ Coordinate W", $InSituCoordinateW);
            if ($InSituCoordinateD) show_row("In Situ Coordinate D", $InSituCoordinateD);
            if ($MunsellColor) show_row("Munsell Color", $MunsellColor);
            if ($ArtifactType) show_row("Artifact Type", get_id_name("artifacttypelist", $ArtifactType));
            if ($SpeciesIdentification) show_row("Species Identification", $SpeciesIdentification);
            if ($CrossReference) show_row("Cross Reference", $CrossReference);
            if ($MaterialType) show_row("Material Type", get_id_name("materialtypelist", $MaterialType));
            if ($Type) show_row("Type", $Type);
            if ($DimensionsL) show_row("Dimensions L", $DimensionsL);
            if ($DimensionsW) show_row("Dimensions W", $DimensionsW);
            if ($DimensionsD) show_row("Dimensions D", $DimensionsD);
            if ($WeightGms) show_row("Weight Gms", $WeightGms);
            if ($Description) show_row("Description", $Description);
            if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?view_image_id=$view_item_id&" . session_name() . "=" . session_id() . "\">click here</a>");
            if ($DateRecovered) show_row("Date Recovered", $DateRecovered);
            if ($Excavator) show_row("Excavator", $Excavator);
            if ($Recorder) show_row("Recorder", $Recorder);
            if ($DateRecorded) show_row("Date Recorded", $DateRecorded);

            //fix this to make them closer to eachother
            if ($PhotoSlide==1) show_row("Slide", "Yes");
            if ($PhotoBlackWhite==1) show_row("Black and White", "Yes");
            if ($PhotoDigital==1) show_row("Digital", "Yes");
            if ($PhotoScan==1) show_row("Scan", "Yes");
            if ($PhotoXerox==1) show_row("Xerox", "Yes");
            
            if ($DateModified) show_row("Date Modified", $DateModified);
            if ($TimeModified) show_row("Time Modified", $TimeModified);

        }
?>
    <tr bgcolor="#dddddd">
        <td align="center"><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?edit_item_id=<?=$view_item_id?>&<?=session_name()?>=<?=session_id()?>">edit this item</a></td>
    </tr>
    <tr bgcolor="#eeeeee">
        <td align="center"><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?<?=session_name()?>=<?=session_id()?>">return to main page</a></td>
    </tr>
</table>
<?
    }
    else {
        header("Location: http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?" . session_name() . "=" . session_id());
        exit;
    }
}
elseif ($del_item_id && (($_SESSION["username"] == "disco") || ($_SESSION["username"] == "rmendoza"))) {
    mysql_query("DELETE FROM {$_SESSION["db_id"]} WHERE ID='$del_item_id'");
    mysql_query("DELETE FROM images WHERE id='$del_item_id' AND db='{$_SESSION["db_id"]}'");
    header("Location: http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?" . session_name() . "=" . session_id());
    exit;
}
elseif ($_SESSION["db_id"]) {
    $page_length = 5;
    if ($_SESSION["db_id"] == "garbage") { 
        $search_sql = $_SESSION["search"] ? " WHERE {$_SESSION["search_field"]} LIKE '%{$_SESSION["search"]}%'" : "";
    }
    elseif ($_SESSION["db_id"] == "stonetools") { 
        $search_sql = $_SESSION["search"] ? " WHERE {$_SESSION["search_field"]} LIKE '%{$_SESSION["search"]}%'" : "";
    }
    else { 
        $sql_array[] = " as a, artifacttypelist as b, materialtypelist as c WHERE a.ArtifactType=b.ID AND a.MaterialType=c.ID ";
        if ($_SESSION["search"]) {
            $sql_array[] = " {$_SESSION["search_field"]} LIKE '%{$_SESSION["search"]}%'";
        }
        $search_sql = implode(" AND ", $sql_array);
    }

    $result = mysql_query("SELECT count(*) AS num_rows FROM {$_SESSION["db_id"]} $search_sql");
    $num_rows = mysql_result($result, 0, "num_rows");
    if ($num_rows > 0) {
        $first_page = 0;
        $last_page = ceil($num_rows/$page_length) - 1;
        $prev_page = (($_SESSION["page"] - 1) < $first_page) ? $first_page : ($_SESSION["page"] - 1);
        $next_page = (($_SESSION["page"] + 1) > $last_page) ? $last_page : ($_SESSION["page"] + 1);
        ### if past the last page, go to the last page
        if ((($_SESSION["page"] * $page_length) + 1) > $num_rows) {
            header("Location: http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?page=$last_page&" . session_name() . "=" . session_id());
            exit;
        }
        elseif ($_SESSION["page"] < 0) {
            header("Location: http://{$_SERVER["SERVER_NAME"]}{$_SERVER["PHP_SELF"]}?page=0&" . session_name() . "=" . session_id());
            exit;
        }
    }

    $page_row = $_POST["submit_search"] ? 0 : ($_SESSION["page"] * $page_length);

    if ($_SESSION["db_id"] == "garbage") { 
        $field_sql = "ID, CatalogNumber, Brand, Material"; 
    }
    elseif ($_SESSION["db_id"] == "stonetools") { 
        $field_sql = "ID, CatalogNumber, Description, UnitNumber"; 
    }
    else { 
        //BUGBUG: If ArtifactType is missing the record doesn't get displayed. Was big problem when importing SJB, as there was no ArtifactType..
        $field_sql = "a.ID as ID, a.CatalogNumber as CatalogNumber, a.Description as Description, b.NAME as ArtifactType, c.NAME as MaterialType, a.UnitNumber as UnitNumber, a.DateRecovered as DateRecovered"; 
    }
    $result = mysql_query("SELECT $field_sql FROM {$_SESSION["db_id"]} $search_sql ORDER BY {$_SESSION["sort"]} {$_SESSION["sort_direction"]} LIMIT $page_row,$page_length");
    
      if ($_SESSION["db_id"] == "garbage")
        $db_title = "Garbology Database";
      if ($_SESSION["db_id"] == "stonetools")
        $db_title = "Stonetools Database";
      if ($_SESSION["db_id"] == "carmel")
        $db_title = "Carmel Database";
      if ($_SESSION["db_id"] == "sjb")
        $db_title = "SJB Database";
      if ($_SESSION["db_id"] == "contract")
        $db_title = "Contract Sites Database";
    
?>

<table width="480" cellpadding="5" cellspacing="0" border="0">
<font color="#CC3333"><h5><?=$db_title ?></h5></font>
    <tr bgcolor="#eeeeee">
        <form method="post" action="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?<?=session_name()?>=<?=session_id()?>"><td colspan="3" align="center"><input type="text" name="search" value="<?=$_SESSION["search"]?>" size="13"><select name="search_field">
<?
    if ($_SESSION["db_id"] == "garbage") { 
?> 
        <option value="CatalogNumber"<?if ($_SESSION["search_field"] == "CatalogNumber") { echo " SELECTED"; }?>>CatalogNumber</option> 
        <option value="Brand"<?if ($_SESSION["search_field"] == "Brand") { echo " SELECTED"; }?>>Brand</option> 
        <option value="Material"<?if ($_SESSION["search_field"] == "Material") { echo " SELECTED"; }?>>Material</option>
<? 
    }
    elseif ($_SESSION["db_id"] == "stonetools") { 
?> 
        <option value="CatalogNumber"<?if ($_SESSION["search_field"] == "CatalogNumber") { echo " SELECTED"; }?>>CatalogNumber</option> 
        <option value="Description"<?if ($_SESSION["search_field"] == "Description") { echo " SELECTED"; }?>>Description</option> 
        <option value="UnitNumber"<?if ($_SESSION["search_field"] == "UnitNumber") { echo " SELECTED"; }?>>UnitNumber</option>
<? 
    }
    else { 
?> 
        <option value="a.CatalogNumber"<?if ($_SESSION["search_field"] == "a.CatalogNumber") { echo " SELECTED"; }?>>CatalogNumber</option> 
        <option value="a.Description"<?if (!$_SESSION["search_field"] || ($_SESSION["search_field"] == "a.Description")) { echo " SELECTED"; }?>>Description</option> 
        <option value="b.NAME"<?if ($_SESSION["search_field"] == "b.NAME") { echo " SELECTED"; }?>>ArtifactType</option> 
        <option value="c.NAME"<?if ($_SESSION["search_field"] == "c.NAME") { echo " SELECTED"; }?>>MaterialType</option> 
        <option value="a.UnitNumber"<?if ($_SESSION["search_field"] == "a.UnitNumber") { echo " SELECTED"; }?>>UnitNumber</option> 
        <option value="a.DateRecovered"<?if ($_SESSION["search_field"] == "a.DateRecovered") { echo " SELECTED"; }?>>DateRecovered</option>
<? 
    } 
?>
        </select><br><input type="submit" name="submit_search" value="Search"><input type="<?=$_SESSION["search"] ? "submit" : "button"?>" name="submit_search_reset" value="Reset"></td></form>
    </tr>
<?
    if (mysql_num_rows($result) > 0) { ### some items found, list them
?>
    <tr bgcolor="#dddddd">
        <td align="center"><strong><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?sort=CatalogNumber&sort_direction=<?=(($_SESSION["sort_direction"] == "ASC") && ($_SESSION["sort"] == "CatalogNumber")) ? "DESC" : "ASC"?>&<?=session_name()?>=<?=session_id()?>">Cat&nbsp;#</a><?if ($_SESSION["sort"] == "CatalogNumber") {?>&nbsp;<img src="images/<?=strtolower($_SESSION["sort_direction"])?>_order.gif"><?}?></strong></td>
<?
        if ($_SESSION["db_id"] == "garbage") {
?>
        <td align="center"><strong><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?sort=Brand&sort_direction=<?=(($_SESSION["sort_direction"] == "ASC") && ($_SESSION["sort"] == "Brand")) ? "DESC" : "ASC"?>&<?=session_name()?>=<?=session_id()?>">Brand</a><?if ($_SESSION["sort"] == "Brand") {?>&nbsp;<img src="images/<?=strtolower($_SESSION["sort_direction"])?>_order.gif"><?}?></strong></td>
<?
        }
        else {
?>
        <td align="center"><strong><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?sort=Description&sort_direction=<?=(($_SESSION["sort_direction"] == "ASC") && ($_SESSION["sort"] == "Description")) ? "DESC" : "ASC"?>&<?=session_name()?>=<?=session_id()?>">Description</a><?if ($_SESSION["sort"] == "Description") {?>&nbsp;<img src="images/<?=strtolower($_SESSION["sort_direction"])?>_order.gif"><?}?></strong></td>
<?
        }
?>
        <td>&nbsp;</td>
    </tr>
<?
        while ($row = mysql_fetch_assoc($result)) {
            if ($_SESSION["db_id"] == "garbage") {
                $brand = $_SESSION["search"] ? preg_replace("/(\b[^\s]*?)({$_SESSION["search"]})([^\s]*?\b)/i", "<strong>\\1<u>\\2</u>\\3</strong>", $row["Brand"]) : $row["Brand"];
            }
            elseif ($_SESSION["db_id"] == "stonetools") {
                $description = ($_SESSION["search"] && ($_SESSION["search_field"] == "Description")) ? preg_replace("/(\b[^\s]*?)({$_SESSION["search"]})([^\s]*?\b)/i", "<strong>\\1<u>\\2</u>\\3</strong>", $row["Description"]) : $row["Description"];
            }
            else {
                $description = ($_SESSION["search"] && ($_SESSION["search_field"] == "a.Description")) ? preg_replace("/(\b[^\s]*?)({$_SESSION["search"]})([^\s]*?\b)/i", "<strong>\\1<u>\\2</u>\\3</strong>", $row["Description"]) : $row["Description"];
            }
?>
    <tr<?if ($row_index % 2) {?> bgcolor="#eeeeee"<?}?>>
        <td align="center" valign="top"><?=$row["CatalogNumber"]?></td><td valign="top"><?=($_SESSION["db_id"] == "garbage") ? $brand : $description?></td><td valign="top"><a href="index.php?view_item_id=<?=$row["ID"]?>&<?=session_name()?>=<?=session_id()?>">view</a><br><a href="index.php?edit_item_id=<?=$row["ID"]?>&<?=session_name()?>=<?=session_id()?>">edit</a><?if ($_SESSION["username"] == "disco" || $_SESSION["username"] == "rmendoza") {?><br><a href="index.php?del_item_id=<?=$row["ID"]?>&<?=session_name()?>=<?=session_id()?>">delete</a><?}?></td>
    </tr>
<?
            $row_index++;
        }
    }
    elseif ($_SESSION["search"]) { ### no items found after a search
?>
    <tr>
        <td colspan="3" align="center">'<?=htmlentities($_SESSION["search"])?>' returned no results</td>
    </tr>
<?
    }
    else {
?>
    <tr>
        <td colspan="3" align="center">no items in '<?=$_SESSION["db_id"]?>' database</td>
    </tr>
<?
    }
?>
</table>
<table width="480" cellpadding="5" cellspacing="0" border="0">
<?
    if ($num_rows > $page_length) {
        $two_more_pages = ((($next_page * $page_length) + $page_length) < $num_rows) ? true : false;
        $one_more_page = (((($_SESSION["page"] * $page_length) + $page_length) < $num_rows) && !$two_more_pages) ? true : false;
        $any_more_pages = ((($_SESSION["page"] * $page_length) + $page_length) < $num_rows) ? true : false;
        $first_page_rows = $page_length;
        $prev_page_rows = $page_length;
        $next_page_rows = ($next_page != $last_page) ? $page_length : ($num_rows - ($next_page * $page_length));
        $last_page_rows = $num_rows - ($last_page * $page_length);
?>
    <tr bgcolor="#dddddd">
        <td width="18%" align="center"><?if ($_SESSION["page"] > 0) {?><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?page=<?=$first_page?>&<?=session_name()?>=<?=session_id()?>"><?}?>&lt;&lt;<?if ($_SESSION["page"] > 0) {?></a><?}?></td>
        <td width="18%" align="center"><?if ($_SESSION["page"] > 0) {?><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?page=<?=$prev_page?>&<?=session_name()?>=<?=session_id()?>"><?}?>&lt;<?if ($_SESSION["page"] > 0) {?></a><?}?></td>
        <td width="18%" align="center">page&nbsp;<?=($_SESSION["page"] + 1)?>/<?=($last_page + 1)?></td>
        <td width="18%" align="center"><?if ($any_more_pages) {?><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?page=<?=$next_page?>&<?=session_name()?>=<?=session_id()?>"><?}?>&gt;<?if ($any_more_pages) {?></a><?}?></td>
        <td width="18%" align="center"><?if ($any_more_pages) {?><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?page=<?=$last_page?>&<?=session_name()?>=<?=session_id()?>"><?}?>&gt;&gt;<?if ($any_more_pages) {?></a><?}?></td>
    </tr>
<?
    }
?>
    <tr bgcolor="#eeeeee">
        <form method="post" action="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?edit_item_id=-1&<?=session_name()?>=<?=session_id()?>"><td colspan="3" align="left"><input type="submit" name="submit_new" value="New Record"></td></form>
        <form method="post" action="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?<?=session_name()?>=<?=session_id()?>"><td colspan="2" align="right"><input type="submit" name="submit_log_out" value="Log Out"></td></form>
    </tr>
</table>
<?
}
else {
    if ($_GET["about"]) {
?>
<table width="480" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#dddddd">
        <td align="center"><strong>About Screen</strong></td>
    </tr>
    <tr>
        <td align="center">
        <strong>PDA Site Catalog Tool</strong><br>
        <br>
        by<br>
        <strong>Erik Friend</strong><br>
        and<br>
        <strong>Christian Graves</strong><br>
        <br>
        Database design by<br>
        <strong>Ruben G. Mendoza, Ph.D</strong>
        </td>
    </tr>
    <tr bgcolor="#dddddd">
        <td align="center"><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?<?=session_name()?>=<?=session_id()?>">return to log in page</a></td>
    </tr>
</table>
<?
    }
    else {
?>
<form method="post" action="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?<?=session_name()?>=<?=session_id()?>">
<table width="480" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#dddddd">
        <td align="center"><strong>Login Screen</strong></td>
    </tr>
<?
	    if (sizeof($error)) {
?>
    <tr bgcolor="#eeeeee">
        <td align="center"><font color="red"><?=implode("<br>", $error)?></font></td>
    </tr>
<?
        }
?>
    <tr>
        <td align="center"><strong>Database</strong><br><select name="db_id"><option value="carmel"<?if ($form_db_id == "carmel") {?> SELECTED<?}?>>Mission Carmel</option><option value="sjb"<?if ($form_db_id == "sjb") {?> SELECTED<?}?>>San Juan Bautista</option><option value="presidio"<?if ($form_db_id == "presidoo") {?> SELECTED<?}?>>Royal Presidio of Monterey</option><option value="soledad"<?if ($form_db_id == "soledad") {?> SELECTED<?}?>>Soledad Mission</option><option value="garbage"<?if ($form_db_id == "garbage") {?> SELECTED<?}?>>Garbology</option><option value="stonetools"<?if ($form_db_id == "stonetools") {?> SELECTED<?}?>>Stonetools</option><option value="contract"<?if ($form_db_id == "contract") {?> SELECTED<?}?>>Contract Sites</option></select></td>
    </tr>
    <tr>
        <td align="center"><strong>Username</strong><br><input type="text" name="username" value="<?=$form_username?>"></td>
    </tr>
    <tr>
        <td align="center"><strong>Password</strong><br><input type="password" name="password" value="<?=$form_password?>"></td>
    </tr>
    <tr bgcolor="#dddddd">
        <td align="center"><input type="submit" name="submit_log_in" value="Log In"></td>
    </tr>
    <tr bgcolor="#eeeeee">
        <td align="center"><a href="http://<?=$_SERVER["SERVER_NAME"]?><?=$_SERVER["PHP_SELF"]?>?about=true&<?=session_name()?>=<?=session_id()?>"">About</a></td>
    </tr>
</table>
</form>
<?
    }
}
?>