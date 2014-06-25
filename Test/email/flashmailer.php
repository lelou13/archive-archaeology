<?PHP

// Recipient Address
// This is the email address that will receive the emails
$Recipient = "gonzalo_nunez@csumb.edu";

// Required form fields
// This is a list of field names that must be filled out for the // form to be sent. Change this according to what you're using
// it for.
$Required = array ("name", "email", "subject", "comments");

/* Ignore form fields
   Use this to omit certain Flash variables from the email
   Note: the ones listed here are variables that we've used in
   the sample Flash movie that we don't want sent! You may want
   to keep these here! */
$Ignored = array ("result","errormessage","junk");

/* Check our data for Required and Ignored elements */
  /* Check that each required element has been completed */
  while (list($Key, $Value) = each($Required))
  {
    /* If form element has not been passed to script... */
    if ((empty($HTTP_POST_VARS[$Value])) &&
        (empty($HTTP_GET_VARS[$Value])))
    {
        /* Report failure back to Flash */
        $ErrorMsg = urlencode("The $Value field is required!");
        print "&result=fail&errormessage="$ErrorMsg&junk=1";

        /* Stop Here */
        exit;
    }
  }

/* unset() any for element that is to be ignored */
  while (list($Key, $Value) = each($Ignored))
  {
    unset($HTTP_POST_VARS[$Value]);
    unset($HTTP_GET_VARS[$Value]);
  }

/* Build the email to be sent */
  /* Start of mail message */
  $MailMsg = "Hello. The following info has been submitted\n\n";

/* Fetch and process info sent by GET method */
  /* Rewind array pointer */
  reset($HTTP_GET_VARS);

  /* For each element in array... */
  while (list($Key, $Value) = each($HTTP_GET_VARS))
  {
    /* ...append key: value pair to message body */
    $MailMsg .= $Key . ": " . $Value . "\n";
  }

/* Fetch and process all info sent by POST method */
  /* Rewind array pointer */
  reset($HTTP_POST_VARS);

  /* For each element in array... */
  while (list($Key, $Value) = each($HTTP_POST_VARS))
  {
    /* ...append key: value pair to message body */
    $MailMsg .= $Key . ": " . $Value . "\n";
  }

/* Send message using local mail client */
  $success = mail($Recipient, "Visitor Feedback", $MailMsg);

  if($success == true)
  {
    /* Output success result as variable */
    print "&result=okay&junk=1";
  }
  else
  {
    /* mail() function failed. Output error message to Flash */
    $ErrorMsg = urlencode("The mail() function failed.");
    print "&result=fail&errormessage=$ErrorMsg&junk=1";
  }

?>