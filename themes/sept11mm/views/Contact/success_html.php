<?php
	# --- reference or feedback
	$ps_contactType = $this->request->getParameter("contactType", pString);
	if($ps_contactType == "reference"){
?>
<H1><?php print _t("Your Reference Question"); ?></H1>
<h2><?php print _t("Your question has been sent. We will make every effort to meet your deadline, but we cannot guarantee a response in fewer than 10 business days."); ?></H2>
<?php
	}else{
?>
<H1><?php print _t("Feedback"); ?></H1>
<h2><?php print _t("Your feedback has been sent."); ?></H2>
<?php
	
	}
?>