<?
function TopRowArea_onRender() {
	global $session;

	$zone_id = 23;
	$zone_mode = "multi";
	include_once ASP_FRONT_PATH."php/find.region.SI.php";
	$obj_siInRegion = new siInRegionS($zone_id,$zone_mode);
}
?>