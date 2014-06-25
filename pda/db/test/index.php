<?
session_start();

if ($HTTP_POST_VARS["submit_log_out"]) {
    #session_destroy();
    session_unset();
    header("Location: http://archaeology.csumb.edu/pda/db/test/index.php");
}

$hostname    = "proust";
$db_user     = "phpbb";
$db_pass     = "y&sw09lEt";
$db_database = "wireless_db";

$db = mysql_connect("$hostname", "$db_user", "$db_pass") or die("could not connect!");
mysql_select_db($db_database) or die("could not select database!");

if ($HTTP_POST_VARS["submit_log_in"]) {
	$form_username = strtolower($HTTP_POST_VARS["username"]);
	$form_password = $HTTP_POST_VARS["password"];
	$form_db_id = $HTTP_POST_VARS["db_id"];
	if ($form_username && $form_password) {
		$result = mysql_query("SELECT password FROM auth WHERE username='$form_username'");
		if (mysql_num_rows($result) > 0) {
			if ($form_password == mysql_result($result, 0, "password")) {
				#$username = $form_username;
				$_SESSION['username'] = $form_username;
				#$password = $form_password;
				$_SESSION['password'] = $form_password;
				#$db_id = $HTTP_POST_VARS["db_id"];
				$_SESSION['db_id'] = $HTTP_POST_VARS["db_id"];
			}
			else {
				$error[] = "Password incorrect!";
			}
		}
		else {
			$error[] = "User doesn't exist!";
		}
	}
	elseif ($form_username && !$form_password) {
		$error[] = "No password entered!";
	}
	elseif (!$form_username) {
		$error[] = "No username entered!";
	}
    if (sizeof($error)) {
        unset($_SESSION['username']);
        unset($_SESSION['$password']);
        unset($_SESSION['db_id']);
    }
}
else {
	$username = $_SESSION['username'];
	$password = $_SESSION['password'];
	$db_id = $_SESSION['db_id'];
}
$success = $_SESSION['success'];
unset($_SESSION['success']);
#$page = ($HTTP_GET_VARS["page"] || ($HTTP_GET_VARS["page"] == "0")) ? $HTTP_GET_VARS["page"] : $_SESSION['page'];  
$_SESSION['page'] = ($HTTP_GET_VARS["page"] || ($HTTP_GET_VARS["page"] == "0")) ? $HTTP_GET_VARS["page"] : $_SESSION['page'];
#$search = $HTTP_POST_VARS["submit_search"] ? $HTTP_POST_VARS["search"] : ($HTTP_POST_VARS["submit_search_reset"] ? "" : $_SESSION['search']);
$_SESSION['search'] = $HTTP_POST_VARS["submit_search"] ? $HTTP_POST_VARS["search"] : ($HTTP_POST_VARS["submit_search_reset"] ? "" : $_SESSION['search']);
#$sort = $HTTP_GET_VARS["sort"] ? $HTTP_GET_VARS["sort"] : ($_SESSION['sort'] ? $_SESSION['sort'] : "CatalogNumber");
$_SESSION['sort'] = $HTTP_GET_VARS["sort"] ? $HTTP_GET_VARS["sort"] : ($_SESSION['sort'] ? $_SESSION['sort'] : "CatalogNumber");
#$sort_direction = $HTTP_GET_VARS["sort_direction"] ? $HTTP_GET_VARS["sort_direction"] : ($_SESSION['sort_direction'] ? $_SESSION['sort_direction'] : "ASC");
$_SESSION['sort_direction'] = $HTTP_GET_VARS["sort_direction"] ? $HTTP_GET_VARS["sort_direction"] : ($_SESSION['sort_direction'] ? $_SESSION['sort_direction'] : "ASC");
$edit_item_id = $HTTP_GET_VARS["edit_item_id"];
$view_item_id = $HTTP_GET_VARS["view_item_id"];
$edit_image_id = $HTTP_GET_VARS["edit_image_id"];
$view_image_id = $HTTP_GET_VARS["view_image_id"];
$del_item_id = $HTTP_GET_VARS["del_item_id"];
$image_id = $HTTP_GET_VARS["image_id"];

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

if ($image_id) {
    $result = mysql_query("SELECT ImageName, ImageData FROM images WHERE id='$image_id' AND db='$db_id'");
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
<table width="240" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#dddddd">
        <td align="center"><strong>View Image</strong></td>
    </tr>
</table>
<table width="240" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td align="center"><img src="<?=$PHP_SELF?>?image_id=<?=$view_image_id?><?=$edit_image_id?>"></td>
    </tr>
</table>
<table width="240" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#dddddd">
        <td align="center"><a href="<?=$PHP_SELF?>?<?if ($edit_image_id) {?>edit_item_id=<?=$edit_image_id?><?}?><?if ($view_image_id) {?>view_item_id=<?=$view_image_id?><?}?>">return to '<?=$view_image_id ? "view" : "edit"?> item' page</a></td>
    </tr>
</table>
<?

}
elseif ($edit_item_id || ($edit_item_id == "-1")) {
    if ($db_id == "garbage" ) {
      $result = mysql_query("SELECT CatalogNumber, TotalWeight, DateRecorded, Source, Recorder, Count, FluidOunces, SolidOunces, Cost, WasteGms, Brand, Type, Material, Totals, TimeModified, DateModified, ID FROM $db_id WHERE ID='$edit_item_id'");
    } else {
      $result = mysql_query("SELECT CatalogNumber, UnitNumber, UnitLevel, Objective, ArtifactType, DateRecovered, FSNumber, SiteName, SiteDesignation, County, State, MunsellColor, InSituCoordinateN, InSituCoordinateW, InSituCoordinateD, Description, Type, MaterialType, SpeciesIdentification, DimensionsL, DimensionsW, DimensionsD, WeightGms, DateRecorded, Recorder, Excavator, TimeModified, DateModified, UnitQuad, CrossReference, Screen, ID FROM $db_id WHERE ID='$edit_item_id'");
    }

    if ((mysql_num_rows($result) > 0)) {
        $result_image = mysql_query("SELECT ImageName FROM images WHERE id='$edit_item_id' AND db='$db_id'");
        if (mysql_num_rows($result_image) > 0) {
            $ImageName = mysql_result($result_image, 0, "ImageName");
        }
        else {
            $ImageName = "";
        }
        mysql_free_result($result_image);

        if ($db_id == "garbage" ) {

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
          $TimeModified =  mysql_result($result, 0, "TimeModified");
          $DateModified =  mysql_result($result, 0, "DateModified");

        } else {

          $ImageData = "";
          $CatalogNumber = mysql_result($result, 0, "CatalogNumber");
          $UnitNumber = mysql_result($result, 0, "UnitNumber");
          $UnitLevel = mysql_result($result, 0, "UnitLevel");
          $Objective =  mysql_result($result, 0, "Objective");
          $ArtifactType =  mysql_result($result, 0, "ArtifactType");
          $DateRecovered =  mysql_result($result, 0, "DateRecovered");
          $FSNumber =  mysql_result($result, 0, "FSNumber");
          $SiteName =  mysql_result($result, 0, "SiteName");
          $SiteDesignation =  mysql_result($result, 0, "SiteDesignation");
          $County =  mysql_result($result, 0, "County");
          $State =  mysql_result($result, 0, "State");
          $MunsellColor =  mysql_result($result, 0, "MunsellColor");
          $InSituCoordinateN =  mysql_result($result, 0, "InSituCoordinateN");
          $InSituCoordinateW =  mysql_result($result, 0, "InSituCoordinateW");
          $InSituCoordinateD =  mysql_result($result, 0, "InSituCoordinateD");
          $Description =  mysql_result($result, 0, "Description");
          $Type =  mysql_result($result, 0, "Type");
          $MaterialType =  mysql_result($result, 0, "MaterialType");
          $SpeciesIdentification =  mysql_result($result, 0, "SpeciesIdentification");
          $DimensionsL =  mysql_result($result, 0, "DimensionsL");
          $DimensionsW =  mysql_result($result, 0, "DimensionsW");
          $DimensionsD =  mysql_result($result, 0, "DimensionsD");
          $WeightGms =  mysql_result($result, 0, "WeightGms");
          $DateRecorded =  mysql_result($result, 0, "DateRecorded");
          $Recorder =  mysql_result($result, 0, "Recorder");
          $Excavator =  mysql_result($result, 0, "Excavator");
          $TimeModified =  mysql_result($result, 0, "TimeModified");
          $DateModified =  mysql_result($result, 0, "DateModified");
          $UnitQuad =  mysql_result($result, 0, "UnitQuad");
          $CrossReference =  mysql_result($result, 0, "CrossReference");
          $Screen =  mysql_result($result, 0, "Screen");
      }
    }
    elseif ($edit_item_id != "-1") {
        header("Location: http://archaeology.csumb.edu/pda/db/test/index.php");
    } else {

      if ($db_id == "garbage" ) {

        $ImageName = "";
        $ImageData = "";
        $CatalogNumber = "";
        $TotalWeight = "";
        $DateRecorded = "";
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

      } else {

        $ImageName = "";
        $ImageData = "";
        $CatalogNumber = "";
        $UnitNumber = "";
        $UnitLevel = "";
        $Objective = "";
        $ArtifactType = "";
        $DateRecovered = "";
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
        $Recorder = "";
        $Excavator = "";
        $TimeModified = "";
        $DateModified = "";
        $UnitQuad = "";
        $CrossReference = "";
        $Screen = "";
      }
    }

    if ($HTTP_POST_VARS["submit_item"] && ($HTTP_POST_VARS["unique_token"] == $_SESSION['unique_token'])) {
        unset($_SESSION['unique_token'];  ### prevents double form submission.  a unique key is stored in the session and the form.  when the form is submitted the first time, those keys match, and the script erases the key from the session.  if the form is submitted twice, the key in the session is already gone, so they no longer match, and no updates will be processed.
        $form_ImageNameTmp = $HTTP_POST_FILES['ImageName']['tmp_name'];
        $form_ImageName = $HTTP_POST_FILES['ImageName']['name'];
        $form_ImageSize = $HTTP_POST_FILES['ImageName']['size'];

      if ($db_id == "garbage" ) {

          $form_CatalogNumber = $HTTP_POST_VARS["CatalogNumber"];
          $form_TotalWeight = $HTTP_POST_VARS["TotalWeight"];
          $form_DateRecorded = $HTTP_POST_VARS["DateRecorded"];
          $form_Source = $HTTP_POST_VARS["Source"];
          $form_Recorder = $HTTP_POST_VARS["Recorder"];
          $form_Count = $HTTP_POST_VARS["Count"];
          $form_FluidOunces = $HTTP_POST_VARS["FluidOunces"];
          $form_SolidOunces = $HTTP_POST_VARS["SolidOunces"];
          $form_Cost = $HTTP_POST_VARS["Cost"];
          $form_WasteGms = $HTTP_POST_VARS["WasteGms"];
          $form_Brand = $HTTP_POST_VARS["Brand"];
          $form_Type = $HTTP_POST_VARS["Type"];
          $form_Material = $HTTP_POST_VARS["Material"];
          $form_Totals = $HTTP_POST_VARS["Totals"];
          $form_TimeModified = $HTTP_POST_VARS["TimeModified"];
          $form_DateModified = $HTTP_POST_VARS["DateModified"];

      } else {

          $form_CatalogNumber = $HTTP_POST_VARS["CatalogNumber"];
          $form_UnitNumber = $HTTP_POST_VARS["UnitNumber"];
          $form_UnitLevel = $HTTP_POST_VARS["UnitLevel"];
          $form_Objective = $HTTP_POST_VARS["Objective"];
          $form_ArtifactType = $HTTP_POST_VARS["ArtifactType"];
          $form_DateRecovered = $HTTP_POST_VARS["DateRecovered"];
          $form_FSNumber = $HTTP_POST_VARS["FSNumber"];
          $form_SiteName = $HTTP_POST_VARS["SiteName"];
          $form_SiteDesignation = $HTTP_POST_VARS["SiteDesignation"];
          $form_County = $HTTP_POST_VARS["County"];
          $form_State = $HTTP_POST_VARS["State"];
          $form_MunsellColor = $HTTP_POST_VARS["MunsellColor"];
          $form_InSituCoordinateN = $HTTP_POST_VARS["InSituCoordinateN"];
          $form_InSituCoordinateW = $HTTP_POST_VARS["InSituCoordinateW"];
          $form_InSituCoordinateD = $HTTP_POST_VARS["InSituCoordinateD"];
          $form_Description = $HTTP_POST_VARS["Description"];
          $form_Type = $HTTP_POST_VARS["Type"];
          $form_MaterialType = $HTTP_POST_VARS["MaterialType"];
          $form_SpeciesIdentification = $HTTP_POST_VARS["SpeciesIdentification"];
          $form_DimensionsL = $HTTP_POST_VARS["DimensionsL"];
          $form_DimensionsW = $HTTP_POST_VARS["DimensionsW"];
          $form_DimensionsD = $HTTP_POST_VARS["DimensionsD"];
          $form_WeightGms = $HTTP_POST_VARS["WeightGms"];
          $form_DateRecorded = $HTTP_POST_VARS["DateRecorded"];
          $form_Recorder = $HTTP_POST_VARS["Recorder"];
          $form_Excavator = $HTTP_POST_VARS["Excavator"];
          $form_TimeModified = $HTTP_POST_VARS["TimeModified"];
          $form_DateModified = $HTTP_POST_VARS["DateModified"];
          $form_UnitQuad = $HTTP_POST_VARS["UnitQuad"];
          $form_CrossReference = $HTTP_POST_VARS["CrossReference"];
          $form_Screen = $HTTP_POST_VARS["Screen"];
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

        if ($db_id == "garbage" ) {

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

        } else {

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
        }

        if (sizeof($item_sql_array)) { ### if anything requires updating
            if (!sizeof($error)) { ### if no error in validation, do db inserts/updates
                $item_sql = implode(", ", $item_sql_array);
                if ($edit_item_id == "-1") { ### if this is a new item, make a blank row in the db and get its id, if its not new, an id will already exist
                    ### insert new item id
                    mysql_query("INSERT INTO $db_id (DateRecorded) VALUES (NOW())");
                    $edit_item_id = mysql_insert_id();
                    if (!$form_Recorder) {
                        mysql_query("UPDATE $db_id SET Recorder='$username' WHERE ID='$edit_item_id'");
                    }
                }

                if ($form_ImageNameTmp && $form_ImageName) { ### if an image was uploaded
                    if (($form_ImageSize == 0) || ($form_ImageSize > 250000)) {
                        $error[] = "Image failed to upload!<br>Might be too large (250k max)";
                    }
                    else {
                        $form_ImageData = addslashes(fread(fopen($form_ImageNameTmp, "rb"), filesize($form_ImageNameTmp)));
                        if ($ImageName) { ## image already in db
                            mysql_query("UPDATE images SET ImageName='$form_ImageName', ImageData='$form_ImageData' WHERE id='$edit_item_id' AND db='$db_id'");
                        }
                        else {
                            mysql_query("INSERT INTO images (id, db, ImageName, ImageData) VALUES ('$edit_item_id', '$db_id', '$form_ImageName', '$form_ImageData')");
                        }
                        $success[] = "Image updated!";
                        $_SESSION['success'];
                    }
                }

                if (!sizeof($error)) {
                    ### update the db with form fields
                    mysql_query("UPDATE $db_id SET $item_sql WHERE ID='$edit_item_id'");
                    $success[] = "Item #$form_CatalogNumber updated!";
                    $_SESSION['success'];
                    header("Location: http://archaeology.csumb.edu/pda/db/test/index.php?view_item_id=$edit_item_id");
                }
            }
        }
    }

    $_SESSION['unique_token'] = = substr(ereg_replace("[^A-Za-z]", "", crypt(time())) . ereg_replace("[^A-Za-z]", "", crypt(time())) . ereg_replace("[^A-Za-z]", "", crypt(time())), 0, 8);
?>
<form method="post" enctype="multipart/form-data" action="<?=$PHP_SELF?>?edit_item_id=<?=$edit_item_id?>">
<input type="hidden" name="MAX_FILE_SIZE" value="250000"> 
<input type="hidden" name="unique_token" value="<?=$unique_token?>">
<table width="240" cellpadding="5" cellspacing="0" border="0">
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

    if ($db_id == "garbage" ) {

      show_row("Catalog Number", get_textbox("CatalogNumber", $CatalogNumber));
      show_row("Total Weight", get_textbox("TotalWeight", $TotalWeight));
      show_row("Collection Date", get_textbox("DateRecorded", $DateRecorded));
      show_row("Source", get_textbox("Source", $Source));
      show_row("Recorder", get_textbox("Recorder", $Recorder));
      show_row("Count", get_textbox("Count", $Count));
      show_row("Fluid Ounces", get_textbox("FluidOunces", $FluidOunces));
      show_row("Solid Ounces", get_textbox("SolidOunces", $SolidOunces));
      show_row("Cost", get_textbox("Cost", $Cost));
      show_row("Waste gms.", get_textbox("WasteGms", $WasteGms));
      show_row("Brand", get_textbox("Brand", $Brand));
      show_row("Type", get_textbox("Type", $Type));
      show_row("Material", get_textbox("Material", $Material));
      show_row("Totals", get_textbox("Totals", $Totals));
      if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"$PHP_SELF?edit_image_id=$edit_item_id\">click here</a>");    
      show_row(($ImageName ? "Replace " : "Upload ") . "Image", get_file("ImageName", $ImageName));
      show_row("Date Modified", $DateModified);
      show_row("Time Modified", $TimeModified);

    } else {

      show_row("Catalog Number", get_textbox("CatalogNumber", $CatalogNumber));
      show_row("Description", get_textarea("Description", $Description));
      if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"$PHP_SELF?edit_image_id=$edit_item_id\">click here</a>");
      show_row(($ImageName ? "Replace " : "Upload ") . "Image", get_file("ImageName", $ImageName));
      show_row("Unit Number", get_textbox("UnitNumber", $UnitNumber));
      show_row("Unit Level", get_select("UnitLevel", "unitlevellist", $UnitLevel));
      show_row("Objective", get_select("Objective", "objectivelist", $Objective));
      show_row("Artifact Type", get_select("ArtifactType", "artifacttypelist", $ArtifactType));
      show_row("Date Recovered", get_textbox("DateRecovered", $DateRecovered));
      show_row("FS Number", get_select("FSNumber", "fslist", $FSNumber));
      show_row("Site Name", get_select("SiteName", "sitenamelist", $SiteName));
      show_row("Site Designation", get_select("Site Designation", "sitedesignationlist", $SiteDesignation));
      show_row("County", get_select("County", "countylist", $County));
      show_row("State", get_select("State", "statelist",  $State));
      show_row("Munsell Color", get_textbox("MunsellColor", $MunsellColor));
      show_row("In Situ Coordinate N", get_textbox("InSituCoordinateN", $InSituCoordinateN));
      show_row("In Situ Coordinate W", get_textbox("InSituCoordinateW", $InSituCoordinateW));
      show_row("In Situ Coordinate D", get_textbox("InSituCoordinateD", $InSituCoordinateD));
      show_row("Type", get_textbox("Type", $Type));
      show_row("Material Type", get_select("MaterialType", "materialtypelist", $MaterialType));
      show_row("Species Identification", get_textbox("SpeciesIdentification", $SpeciesIdentification));
      show_row("Dimensions L", get_textbox("DimensionsL", $DimensionsL));
      show_row("Dimensions W", get_textbox("DimensionsW", $DimensionsW));
      show_row("Dimensions D", get_textbox("DimensionsD", $DimensionsD));
      show_row("Weight gms.", get_textbox("WeightGms", $WeightGms));
      show_row("Excavator", get_textbox("Excavator", $Excavator));
      show_row("Unit Quad", get_select("UnitQuad", "unitquadlist", $UnitQuad));
      show_row("Cross Reference", get_textbox("CrossReference", $CrossReference));
      show_row("Screen", get_textbox("Screen", $Screen));
      show_row("Date Recorded", get_textbox("DateRecorded", $DateRecorded));
      show_row("Recorder", get_textbox("Recorder", $Recorder));
      show_row("Date Modified", $DateModified);
      show_row("Time Modified", $TimeModified);
    }
?>
    <tr bgcolor="#eeeeee">
        <td align="left"><table width="100%" cellpadding="0" cellspacing="0"><tr><td align="left"><input type="reset" name="reset" value="Reset Form"></td><td align="right"><input type="submit" name="submit_item" value="Update Item"></td></tr></table></td>
    </tr>
    <tr bgcolor="#dddddd">
        <td align="center"><a href="<?=$PHP_SELF?>?view_item_id=<?=$edit_item_id?>">view this item</a></td>
    </tr>
    <tr bgcolor="#eeeeee">
        <td align="center"><a href="<?=$PHP_SELF?>">return to main page</a></td>
    </tr>
</table>
</form>
<?
}
elseif ($view_item_id) {

    if ($db_id == "garbage" ) {

      $result = mysql_query("SELECT CatalogNumber, TotalWeight, DateRecorded, Source, Recorder, Count, FluidOunces, SolidOunces, Cost, WasteGms, Brand, Type, Material, Totals, TimeModified, DateModified, ID FROM $db_id WHERE ID='$view_item_id'");

    } else {

      $result = mysql_query("SELECT CatalogNumber, UnitNumber, UnitLevel, Objective, ArtifactType, DateRecovered, FSNumber, SiteName, SiteDesignation, County, State, MunsellColor, InSituCoordinateN, InSituCoordinateW, InSituCoordinateD, Description, Type, MaterialType, SpeciesIdentification, DimensionsL, DimensionsW, DimensionsD, WeightGms, DateRecorded, Recorder, Excavator, TimeModified, DateModified, UnitQuad, CrossReference, Screen, ID FROM $db_id WHERE ID='$view_item_id'");

    }

    if (mysql_num_rows($result) > 0) {
      $result_image = mysql_query("SELECT ImageName, ImageData FROM images WHERE id='$view_item_id' AND db='$db_id'");

        if (mysql_num_rows($result_image) > 0) {
            $ImageName = mysql_result($result_image, 0, "ImageName");
            $ImageData = mysql_result($result_image, 0, "ImageData");
        }
        mysql_free_result($result_image);

        if ($db_id == "garbage" ) {

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
          $TimeModified =  mysql_result($result, 0, "TimeModified");
          $DateModified =  mysql_result($result, 0, "DateModified");

        } else {

          $CatalogNumber = mysql_result($result, 0, "CatalogNumber");
          $UnitNumber = mysql_result($result, 0, "UnitNumber");
          $UnitLevel = mysql_result($result, 0, "UnitLevel");
          $Objective =  mysql_result($result, 0, "Objective");
          $ArtifactType =  mysql_result($result, 0, "ArtifactType");
          $DateRecovered =  mysql_result($result, 0, "DateRecovered");
          $FSNumber =  mysql_result($result, 0, "FSNumber");
          $SiteName =  mysql_result($result, 0, "SiteName");
          $SiteDesignation =  mysql_result($result, 0, "SiteDesignation");
          $County =  mysql_result($result, 0, "County");
          $State =  mysql_result($result, 0, "State");
          $MunsellColor =  mysql_result($result, 0, "MunsellColor");
          $InSituCoordinateN =  mysql_result($result, 0, "InSituCoordinateN");
          $InSituCoordinateW =  mysql_result($result, 0, "InSituCoordinateW");
          $InSituCoordinateD =  mysql_result($result, 0, "InSituCoordinateD");
          $Description =  mysql_result($result, 0, "Description");
          $Type =  mysql_result($result, 0, "Type");
          $MaterialType =  mysql_result($result, 0, "MaterialType");
          $SpeciesIdentification =  mysql_result($result, 0, "SpeciesIdentification");
          $DimensionsL =  mysql_result($result, 0, "DimensionsL");
          $DimensionsW =  mysql_result($result, 0, "DimensionsW");
          $DimensionsD =  mysql_result($result, 0, "DimensionsD");
          $WeightGms =  mysql_result($result, 0, "WeightGms");
          $DateRecorded =  mysql_result($result, 0, "DateRecorded");
          $Recorder =  mysql_result($result, 0, "Recorder");
          $Excavator =  mysql_result($result, 0, "Excavator");
          $TimeModified =  mysql_result($result, 0, "TimeModified");
          $DateModified =  mysql_result($result, 0, "DateModified");
          $UnitQuad =  mysql_result($result, 0, "UnitQuad");
          $CrossReference =  mysql_result($result, 0, "CrossReference");
          $Screen =  mysql_result($result, 0, "Screen");
        }
?>
<table width="240" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#dddddd">
        <td align="center"><strong>View Item</strong></td>
    </tr>
<?
    if (sizeof($success)) {
?>
    <tr bgcolor="#eeeeee">
        <td align="center"><font color="green"><?=implode("<br>", $success)?></font></td>
    </tr>
<?
    }

        if ($db_id == "garbage" ) {

          if ($CatalogNumber) show_row("CatalogNumber", $CatalogNumber);
          if ($TotalWeight) show_row("TotalWeight", $TotalWeight);
          if ($DateRecorded) show_row("DateRecorded", $DateRecorded);
          if ($Source) show_row("Source", $Source);
          if ($Recorder) show_row("Recorder", $Recorder);
          if ($Count) show_row("Count", $Count);
          if ($FluidOunces) show_row("FluidOunces", $FluidOunces);
          if ($SolidOunces) show_row("SolidOunces", $SolidOunces);
          if ($Cost) show_row("Cost", $Cost);
          if ($WasteGms) show_row("WasteGms", $WasteGms);
          if ($Brand) show_row("Brand", $Brand);
          if ($Type) show_row("Type", $Type);
          if ($Material) show_row("Material", $Material);
          if ($Totals) show_row("Totals", $Totals);
          if ($DateModified) show_row("DateModified", $DateModified);
          if ($TimeModified) show_row("TimeModified", $TimeModified);
          if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"$PHP_SELF?view_image_id=$view_item_id\">click here</a>");

        } else {

          if ($CatalogNumber) show_row("CatalogNumber", $CatalogNumber);
          if ($Description) show_row("Description", $Description);
          if ($ImageName) show_row("Image" . ($ImageName ? " $ImageName" : ""), "<a href=\"$PHP_SELF?view_image_id=$view_item_id\">click here</a>");
          if ($UnitNumber) show_row("UnitNumber", $UnitNumber);
          if ($UnitLevel) show_row("UnitLevel", get_id_name("unitlevellist", $UnitLevel));
          if ($Objective) show_row("Objective", get_id_name("objectivelist", $Objective));
          if ($ArtifactType) show_row("ArtifactType", get_id_name("artifacttypelist", $ArtifactType));
          if ($DateRecovered) show_row("DateRecovered", $DateRecovered);
          if ($FSNumber) show_row("FSNumber", get_id_name("fslist", $FSNumber));
          if ($SiteName) show_row("SiteName", get_id_name("sitenamelist", $SiteName));
          if ($SiteDesignation) show_row("SiteDesignation", get_id_name("sitedesignationlist", $SiteDesignation));
          if ($County) show_row("County", get_id_name("countylist", $County));
          if ($State) show_row("State", get_id_name("statelist",  $State));
          if ($MunsellColor) show_row("MunsellColor", $MunsellColor);
          if ($InSituCoordinateN) show_row("InSituCoordinateN", $InSituCoordinateN);
          if ($InSituCoordinateW) show_row("InSituCoordinateW", $InSituCoordinateW);
          if ($InSituCoordinateD) show_row("InSituCoordinateD", $InSituCoordinateD);
          if ($Type) show_row("Type", $Type);
          if ($MaterialType) show_row("MaterialType", get_id_name("materialtypelist", $MaterialType));
          if ($SpeciesIdentification) show_row("SpeciesIdentification", $SpeciesIdentification);
          if ($DimensionsL) show_row("DimensionsL", $DimensionsL);
          if ($DimensionsW) show_row("DimensionsW", $DimensionsW);
          if ($DimensionsD) show_row("DimensionsD", $DimensionsD);
          if ($WeightGms) show_row("WeightGms", $WeightGms);
          if ($Excavator) show_row("Excavator", $Excavator);
          if ($UnitQuad) show_row("UnitQuad", get_id_name("unitquadlist", $UnitQuad));
          if ($CrossReference) show_row("CrossReference", $CrossReference);
          if ($Screen) show_row("Screen", $Screen);
          if ($DateRecorded) show_row("DateRecorded", $DateRecorded);
          if ($Recorder) show_row("Recorder", $Recorder);
          if ($DateModified) show_row("DateModified", $DateModified);
          if ($TimeModified) show_row("TimeModified", $TimeModified);
        }
?>
    <tr bgcolor="#dddddd">
        <td align="center"><a href="<?=$PHP_SELF?>?edit_item_id=<?=$view_item_id?>">edit this item</a></td>
    </tr>
    <tr bgcolor="#eeeeee">
        <td align="center"><a href="<?=$PHP_SELF?>">return to main page</a></td>
    </tr>
</table>
<?
    }
    else {
        header("Location: http://archaeology.csumb.edu/pda/db/test/index.php");
    }
}
elseif ($del_item_id && ($username == "disco")) {
    mysql_query("DELETE FROM $db_id WHERE ID='$del_item_id'");
    mysql_query("DELETE FROM images WHERE id='$del_item_id' AND db='$db_id'");
    header("Location: http://archaeology.csumb.edu/pda/db/test/index.php");
}
elseif ($del_item_id && ($username == "rmendoza")) {
    mysql_query("DELETE FROM $db_id WHERE ID='$del_item_id'");
    mysql_query("DELETE FROM images WHERE id='$del_item_id' AND db='$db_id'");
    header("Location: http://archaeology.csumb.edu/pda/db/test/index.php");
}
elseif ($db_id) {
    $page_length = 5;
    $search_sql = $search ? " WHERE Description LIKE '%$search%'" : "";

    $result = mysql_query("SELECT count(*) AS num_rows FROM $db_id $search_sql");
    $num_rows = mysql_result($result, 0, "num_rows");
    $first_page = 0;
    $last_page = ceil($num_rows/$page_length) - 1;
    $prev_page = (($page - 1) < $first_page) ? $first_page : ($page - 1);
    $next_page = (($page + 1) > $last_page) ? $last_page : ($page + 1);
    ### if past the last page, go to the last page
    if ((($page * $page_length) + 1) > $num_rows) {
        header("Location: http://archaeology.csumb.edu/pda/db/test/index.php?page=$last_page");
    }

    $page_row = $page * $page_length;

    if ($db_id == "garbage") {
      $result = mysql_query("SELECT ID, CatalogNumber, Brand FROM $db_id $search_sql ORDER BY $sort $sort_direction LIMIT $page_row,$page_length"); 
    } else {
      $result = mysql_query("SELECT ID, CatalogNumber, Description FROM $db_id $search_sql ORDER BY $sort $sort_direction LIMIT $page_row,$page_length");
    }
?>
<table width="240" cellpadding="5" cellspacing="0" border="0">
    <tr bgcolor="#eeeeee">
        <form method="post" action="<?=$PHP_SELF?>"><td colspan="3" align="left"><input type="text" name="search" value="<?=$search?>" size="13">&nbsp;<input type="submit" name="submit_search" value="Search"><input type="<?=$search ? "submit" : "button"?>" name="submit_search_reset" value="Reset"></td></form>
    </tr>
<?
    if (mysql_num_rows($result) > 0) { ### some items found, list them
?>
    <tr bgcolor="#dddddd">
        <td align="center"><strong><a href="<?=$PHP_SELF?>?sort=CatalogNumber&sort_direction=<?=(($sort_direction == "ASC") && ($sort == "CatalogNumber")) ? "DESC" : "ASC"?>">Cat&nbsp;#</a><?if ($sort == "CatalogNumber") {?>&nbsp;<img src="images/<?=strtolower($sort_direction)?>_order.gif"><?}?></strong></td>
<?
    if ($db_id == "garbage") {
?>
        <td align="center"><strong><a href="<?=$PHP_SELF?>?sort=Brand&sort_direction=<?=(($sort_direction == "ASC") && ($sort == "Brand")) ? "DESC" : "ASC"?>">Brand</a><?if ($sort == "Brand") {?>&nbsp;<img src="images/<?=strtolower($sort_direction)?>_order.gif"><?}?></strong></td>
        <td>&nbsp;</td>
    </tr>
<?
    } else {
?>
        <td align="center"><strong><a href="<?=$PHP_SELF?>?sort=Description&sort_direction=<?=(($sort_direction == "ASC") && ($sort == "Description")) ? "DESC" : "ASC"?>">Description</a><?if ($sort == "Description") {?>&nbsp;<img src="images/<?=strtolower($sort_direction)?>_order.gif"><?}?></strong></td>
        <td>&nbsp;</td>
    </tr>
<?
    }
        while ($row = mysql_fetch_assoc($result)) {
    if ($db_id == "garbage") {
            $brand = $search ? preg_replace("/($search)/i", "<strong><u>\\1</u></strong>", $row["Brand"]) : $row["Brand"];
    } else {
            $description = $search ? preg_replace("/($search)/i", "<strong><u>\\1</u></strong>", $row["Description"]) : $row["Description"];
    }
?>
    <tr<?if ($row_index % 2) {?> bgcolor="#eeeeee"<?}?>>
<?
    if ($db_id == "garbage") {
?>
        <td align="center" valign="top"><?=$row["CatalogNumber"]?></td><td valign="top"><?=$brand?></td><td valign="top"><a href="index.php?view_item_id=<?=$row["ID"]?>">view</a><br><a href="index.php?edit_item_id=<?=$row["ID"]?>">edit</a><?if ($username == "disco" || $username == "rmendoza") {?><br><a href="index.php?del_item_id=<?=$row["ID"]?>">delete</a><?}?></td>
<?
    } else {
?>
        <td align="center" valign="top"><?=$row["CatalogNumber"]?></td><td valign="top"><?=$description?></td><td valign="top"><a href="index.php?view_item_id=<?=$row["ID"]?>">view</a><br><a href="index.php?edit_item_id=<?=$row["ID"]?>">edit</a><?if ($username == "disco" || $username == "rmendoza") {?><br><a href="index.php?del_item_id=<?=$row["ID"]?>">delete</a><?}?></td>
<?
    }
?>
    </tr>
<?
            $row_index++;
        }
    }
    elseif ($search) { ### no items found after a search
?>
    <tr>
        <td colspan="3" align="center">'<?=htmlentities($search)?>' returned no results</td>
    </tr>
<?
    }
    else {
?>
    <tr>
        <td colspan="3" align="center">no items in '<?=$db_id?>' database</td>
    </tr>
<?
    }
?>
</table>
<table width="240" cellpadding="5" cellspacing="0" border="0">
<?
    if ($num_rows > $page_length) {
        $two_more_pages = ((($next_page * $page_length) + $page_length) < $num_rows) ? true : false;
        $one_more_page = (((($page * $page_length) + $page_length) < $num_rows) && !$two_more_pages) ? true : false;
        $any_more_pages = ((($page * $page_length) + $page_length) < $num_rows) ? true : false;
        $first_page_rows = $page_length;
        $prev_page_rows = $page_length;
        $next_page_rows = ($next_page != $last_page) ? $page_length : ($num_rows - ($next_page * $page_length));
        $last_page_rows = $num_rows - ($last_page * $page_length);
?>
    <tr bgcolor="#dddddd">
        <td width="18%" align="center"><?if ($page > 0) {?><a href="<?=$PHP_SELF?>?&page=<?=$first_page?>"><?}?>&lt;&lt;<?if ($page > 0) {?></a><?}?></td>
        <td width="18%" align="center"><?if ($page > 0) {?><a href="<?=$PHP_SELF?>?page=<?=$prev_page?>"><?}?>&lt;<?if ($page > 0) {?></a><?}?></td>
        <td width="18%" align="center">page&nbsp;<?=($page + 1)?>/<?=($last_page + 1)?></td>
        <td width="18%" align="center"><?if ($any_more_pages) {?><a href="<?=$PHP_SELF?>?page=<?=$next_page?>"><?}?>&gt;<?if ($any_more_pages) {?></a><?}?></td>
        <td width="18%" align="center"><?if ($any_more_pages) {?><a href="<?=$PHP_SELF?>?page=<?=$last_page?>"><?}?>&gt;&gt;<?if ($any_more_pages) {?></a><?}?></td>
    </tr>
<?
    }
?>
    <tr bgcolor="#eeeeee">
        <form method="post" action="<?=$PHP_SELF?>?edit_item_id=-1"><td colspan="3" align="left"><input type="submit" name="submit_new" value="New Record"></td></form>
        <form method="post" action="<?=$PHP_SELF?>"><td colspan="2" align="right"><input type="submit" name="submit_log_out" value="Log Out"></td></form>
    </tr>
</table>
<?
}
else {
    if ($HTTP_GET_VARS["about"]) {
?>
<table width="240" cellpadding="5" cellspacing="0" border="0">
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
        <td align="center"><a href="<?=$PHP_SELF?>">return to log in page</a></td>
    </tr>
</table>
<?
    }
    else {
?>
<form method="post" action="<?=$PHP_SELF?>">
<table width="240" cellpadding="5" cellspacing="0" border="0">
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
        <td align="center"><strong>Database</strong><br><select name="db_id"><option value="carmel"<?if ($form_db_id == "carmel") {?> SELECTED<?}?>>carmel</option><option value="sjb"<?if ($form_db_id == "sjb") {?> SELECTED<?}?>>sjb</option><option value="garbage"<?if ($form_db_id == "garbage") {?> SELECTED<?}?>>garbage</option></select></td>
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
        <td align="center"><a href="<?=$PHP_SELF?>?about=true">About</a></td>
    </tr>
</table>
</form>
<?
    }
}
?>