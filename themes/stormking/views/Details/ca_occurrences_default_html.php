<?php
	$t_item = $this->getVar("item");
	$va_comments = $this->getVar("comments");
	$vn_comments_enabled = 	$this->getVar("commentsEnabled");
	$vn_share_enabled = 	$this->getVar("shareEnabled");	
?>
<div class="row">
	<div class='col-xs-12 '>
		<div class="container"><div class="row">
			<div class='col-sm-12'>
				<div class='detailNav'>{{{previousLink}}}{{{resultsLink}}}{{{nextLink}}}</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 ">
				<H4>{{{ca_occurrences.preferred_labels.name}}}</H4>
<?php				
				if ($vs_ex_dates = $t_item->get('ca_occurrences.exhibition_dates')) {
					print "<div>".$vs_ex_dates."</div>";
				}
?>								
			</div>		
		</div>
		<hr style='padding-bottom:5px;'>
		<div class="row">			
			<div class='col-sm-6 col-md-6 col-lg-6'>
<?php
				if ($vs_description = $t_item->get('ca_occurrences.description')) {
					print "<div class='unit'>".$vs_description."</div>";
				}
#				if ($va_venue = $t_item->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('venue')))){
#					print "<div class='unit'><h6>Venue</h6>".$va_venue."</div>";
#				}
				if ($va_rel_programs = $t_item->get('ca_occurrences.related.preferred_labels', array('restrictToTypes' => array('exhibition', 'public_program'), 'returnAsLink' => true, 'delimiter' => '<br/>'))) {
					print "<div class='unit'><h6>Related Programs</h6>".$va_rel_programs."</div>";
				}
				if ($va_related_or_history = $t_item->get('ca_objects.object_id', array('returnAsArray' => true, 'checkAccess' => $va_access_values, 'restrictToTypes' => array('oral_history')))) {
					print '<h6>Related Oral Histories</h6>';
					foreach ($va_related_or_history as $va_id => $va_related_or_history_id) {
						$t_rel_or = new ca_objects($va_related_or_history_id);
						print "<div class='detailLine'>";
						print "<p>".caNavLink($this->request, $t_rel_or->get('ca_objects.preferred_labels'), '', 'Detail', 'oralhistory', $t_rel_or->get('ca_objects.object_id'))."</p>";
						print "</div>";
					}
				}				
								
?>
			</div><!-- end col -->
			<div class='col-md-6 col-lg-6'>
<?php
				if ($va_rep = $t_item->get('ca_objects.object_id', array('restrictToRelationshipTypes' => array('primary_rep'), 'returnAsArray' => true, 'checkAccess' => $va_access_values))) {
					foreach ($va_rep as $va_key => $vn_rep_id) {
						$t_primary = new ca_objects($vn_rep_id);
						print $t_primary->get('ca_object_representations.media.large');
						break;
					}
				}
				if ($va_remarks_images = $t_item->get('ca_occurrences.bibliography', array('returnWithStructure' => true, 'version' => 'medium'))) {
					foreach ($va_remarks_images as $vn_attribute_id => $va_remarks_image_info) {
						foreach ($va_remarks_image_info as $vn_value_id => $va_remarks_image) {
							print "<div class='unit' style='margin-bottom:20px;'>";

							$o_db = new Db();
							$t_element = ca_attributes::getElementInstance('bibliography');
							$vn_media_element_id = $t_element->getElementID('bibliography');							

							$qr_res = $o_db->query('SELECT value_id FROM ca_attribute_values WHERE attribute_id = ? AND element_id = ?', array($vn_value_id, $vn_media_element_id)) ;
							if ($qr_res->nextRow()) {
								print "<div class='zoomIcon'><a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, '', 'Detail', 'GetMediaOverlay', array('id' => $t_item->get("occurrence_id"), 'context' => 'occurrences', 'identifier' => 'attribute:'.$qr_res->get("value_id"), 'overlay' => 1))."\"); return false;'><h6><i class='fa fa-file'></i> View Bibliography </h6></a></div>";
							}
							print "</div>";
						}
					}
				}
				if ($va_checklist_images = $t_item->get('ca_occurrences.checklist', array('returnWithStructure' => true, 'version' => 'medium'))) {
					foreach ($va_checklist_images as $vn_check_attribute_id => $va_checklist_image_info) {
						foreach ($va_checklist_image_info as $vn_check_value_id => $va_checklist_image) {
							print "<div class='unit' style='margin-bottom:20px;'>";

							$o_db = new Db();
							$t_check_element = ca_attributes::getElementInstance('checklist');
							$vn_check_media_element_id = $t_element->getElementID('checklist');							

							$qr_check_res = $o_db->query('SELECT value_id FROM ca_attribute_values WHERE attribute_id = ? AND element_id = ?', array($vn_check_value_id, $vn_check_media_element_id)) ;
							if ($qr_check_res->nextRow()) {
								print "<div class='zoomIcon'><a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, '', 'Detail', 'GetMediaOverlay', array('id' => $t_item->get("occurrence_id"), 'context' => 'occurrences', 'identifier' => 'attribute:'.$qr_check_res->get("value_id"), 'overlay' => 1))."\"); return false;'><h6><i class='fa fa-file'></i> View Checklist </h6></a></div>";
							}
							print "</div>";
						}
					}
				}
				if ($vs_website = $t_item->get('ca_occurrences.exhibition_website')) {
					print "<div class='unit zoomIcon'><h6><i class='fa fa-external-link-square'></i> <a href='".$vs_website."' target='_blank'>View Exhibition Website</a></h6></div>";
				}				
?>			
			</div><!-- end col -->
		</div><!-- end row -->
<?php	
		#Related Artworks	
		if ($va_related_artworks = $t_item->get('ca_objects.object_id', array('returnAsArray' => true, 'checkAccess' => $va_access_values, 'restrictToTypes' => array('loaned_artwork', 'sk_artwork'), 'sort' => 'ca_object_labels.name'))) {
			print '<div class="row objInfo">';
			print "<hr>";

			print '	<div class="col-sm-12"><h6 class="header">Artworks</h6></div>';
			foreach ($va_related_artworks as $va_id => $va_related_artwork_id) {
				$t_rel_obj = new ca_objects($va_related_artwork_id);
				print "<div class='col-sm-3'>";
				print "<div class='relatedArtwork'>";
				print "<div class='relImg'>".caDetailLink($this->request, $t_rel_obj->get('ca_object_representations.media.widepreview', array('checkAccess' => $va_access_values)), '', 'ca_objects', $t_rel_obj->get('ca_objects.object_id'))."</div>";
				print "<p>".$t_rel_obj->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('artist'), 'checkAccess' => $va_access_values))."</p>";
				print "<p>".caDetailLink($this->request, ( $t_rel_obj->get('ca_objects.preferred_labels') == "Untitled" ? $t_rel_obj->get('ca_objects.preferred_labels') : "<i>".$t_rel_obj->get('ca_objects.preferred_labels')."</i>"), '', 'ca_objects', $t_rel_obj->get('ca_objects.object_id'));
				if ($vs_art_date = $t_rel_obj->get('ca_objects.display_date')) {
					print ", ".$vs_art_date;
				}
				print "</p></div>";
				print "</div><!-- end col -->";
			}
			print "</div><!-- end row -->";			
		}
		
		#Related Installation Views
		if ($va_related_install = $t_item->get('ca_objects.related.object_id', array('returnAsArray' => true, 'checkAccess' => $va_access_values, 'restrictToRelationshipTypes' => array('install_photo'), 'sort' => 'ca_object_labels.name'))) {
			print '<div class="row objInfo">';
			print "<hr>";

			print '	<div class="col-sm-12"><h6 class="header">Installation Photos</h6></div>';
			foreach ($va_related_install as $va_id => $va_related_install_id) {
				$t_rel_install = new ca_objects($va_related_install_id);
				print "<div class='col-sm-3'>";
				print "<div class='relatedArtwork'>";
				print "<div class='relImg'>".caDetailLink($this->request, $t_rel_install->get('ca_object_representations.media.widepreview', array('checkAccess' => $va_access_values)), '', 'ca_objects', $t_rel_install->get('ca_objects.object_id'))."</div>";
				print "<p>".$t_rel_install->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('artist'), 'checkAccess' => $va_access_values))."</p>";
				print "<p>".caDetailLink($this->request, $t_rel_install->get('ca_objects.preferred_labels'), '', 'ca_objects', $t_rel_install->get('ca_objects.object_id'));
				print "</p></div>";
				print "</div><!-- end col -->";
			}
			print "</div><!-- end row -->";			
		}
		
		#Related Media
		if ($va_related_media = $t_item->get('ca_objects.related.object_id', array('returnAsArray' => true, 'checkAccess' => $va_access_values, 'restrictToRelationshipTypes' => array('media'), 'sort' => 'ca_object_labels.name'))) {
			print '<div class="row objInfo">';
			print "<hr>";

			print '	<div class="col-sm-12"><h6 class="header">Media</h6></div>';
			foreach ($va_related_media as $va_id => $va_related_media_id) {
				$t_rel_media = new ca_objects($va_related_media_id);
				print "<div class='col-sm-3'>";
				print "<div class='relatedArtwork'>";
				print "<div class='relImg'>".caDetailLink($this->request, $t_rel_media->get('ca_object_representations.media.widepreview', array('checkAccess' => $va_access_values)), '', 'ca_objects', $t_rel_media->get('ca_objects.object_id'))."</div>";
				print "<p>".$t_rel_media->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('artist'), 'checkAccess' => $va_access_values))."</p>";
				print "<p>".caDetailLink($this->request, $t_rel_media->get('ca_objects.preferred_labels'), '', 'ca_objects', $t_rel_media->get('ca_objects.object_id'));
				print "</p></div>";
				print "</div><!-- end col -->";
			}
			print "</div><!-- end row -->";			
		}
		
		#Related Archival Items
		if ($va_related_archival = $t_item->get('ca_objects.related.object_id', array('returnAsArray' => true, 'checkAccess' => $va_access_values, 'restrictToRelationshipTypes' => array('archival_item'), 'sort' => 'ca_object_labels.name'))) {
			print '<div class="row objInfo">';
			print "<hr>";

			print '	<div class="col-sm-12"><h6 class="header">Archival Items</h6></div>';
			foreach ($va_related_archival as $va_id => $va_related_archival_id) {
				$t_rel_archival = new ca_objects($va_related_archival_id);
				print "<div class='col-sm-3'>";
				print "<div class='relatedArtwork'>";
				print "<div class='relImg'>".caDetailLink($this->request, $t_rel_archival->get('ca_object_representations.media.widepreview', array('checkAccess' => $va_access_values)), '', 'ca_objects', $t_rel_archival->get('ca_objects.object_id'))."</div>";
				print "<p>".$t_rel_archival->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('artist'), 'checkAccess' => $va_access_values))."</p>";
				print "<p>".caDetailLink($this->request, $t_rel_archival->get('ca_objects.preferred_labels'), '', 'ca_objects', $t_rel_archival->get('ca_objects.object_id'));
				print "</p></div>";
				print "</div><!-- end col -->";
			}
			print "</div><!-- end row -->";			
		}	
		
		#Related Catalogue
		if ($va_related_catalogue = $t_item->get('ca_objects.related.object_id', array('returnAsArray' => true, 'checkAccess' => $va_access_values, 'restrictToRelationshipTypes' => array('catalogue'), 'sort' => 'ca_object_labels.name'))) {
			print '<div class="row objInfo">';
			print "<hr>";

			print '	<div class="col-sm-12"><h6 class="header">Catalogue</h6></div>';
			foreach ($va_related_catalogue as $va_id => $va_related_catalogue_id) {
				$t_rel_catalogue = new ca_objects($va_related_catalogue_id);
				print "<div class='col-sm-3'>";
				print "<div class='relatedArtwork'>";
				print "<div class='relImg'>".caDetailLink($this->request, $t_rel_catalogue->get('ca_object_representations.media.widepreview', array('checkAccess' => $va_access_values)), '', 'ca_objects', $t_rel_catalogue->get('ca_objects.object_id'))."</div>";
				print "<p>".$t_rel_catalogue->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('artist'), 'checkAccess' => $va_access_values))."</p>";
				print "<p>".caDetailLink($this->request, $t_rel_catalogue->get('ca_objects.preferred_labels'), '', 'ca_objects', $t_rel_catalogue->get('ca_objects.object_id'));
				print "</p></div>";
				print "</div><!-- end col -->";
			}
			print "</div><!-- end row -->";			
		}							
?>				
		
		</div><!-- end container -->
	</div><!-- end col -->
</div><!-- end row -->
<script type='text/javascript'>
	jQuery(document).ready(function() {
		$('.trimText').readmore({
		  speed: 75,
		  maxHeight: 120
		});
	});
</script>