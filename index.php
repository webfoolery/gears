<?php
/* REFERENCES:
TYRE SIZES
	http://www.ctc.org.uk/cyclists-library/components/wheels-tyres/tyre-sizes
SIMILAR TOOL
	http://www.bikecalc.com/gear_ratios
	http://www.whycycle.co.uk/bike-gear-calculator
JQUERY MOBILE
	http://www.w3schools.com/jquerymobile/
	http://demos.jquerymobile.com/1.4.5/forms/
*/

$preselect = false;
// $preselect = true;
?>
<!DOCTYPE html>
<html lang="en-UK">
	<head>
		<meta charset="UTF-8">
		<title>Gear ratio calculator</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<style>
			body {font-family:arial,helvetica,sans-serif;}
			table.ratioTable {border-collapse:collapse;}
			table.ratioTable th, table.ratioTable td {border:1px solid #777;}
			table.ratioTable label {}
			table.ratioTable input {width:4em;}
			.unitSwitcher .ui-slider-switch { width: 9em }
			div.resultBox {text-shadow:none;font-size:0.8em;}
			.cadenceInput {width:200px!important;}
		</style>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				gears = {
					chainringCount: 0,
					sprocketCount: 0,
					chainrings: [],
					sprockets: [],
					wheelDiameter: 0,
					tyreDiameter: 0,
					targetCadence: 0,
					units: metric,
					varDump: function() {
						// console.log(this);
						console.log(JSON.parse(JSON.stringify(this)));
					},
					initialise: function() {
						var self = this;
						$("#clearAll").click(function(){self.clearAll()});
						$("#setMyDefaults").click(function(){
							self.setMyDefaults()
							self.recalculate()
						});
						$("input").change(function(){
							if (this.name.indexOf('targetCadence') === 0) self.targetCadence = Number(this.value);
							// else if (this.name.indexOf('chainring') === 0) self.recalculate();
							// else if (this.name.indexOf('gear') === 0) self.recalculate();
							// else if (this.name.indexOf('unitToggler') === 0) self.recalculate();
							self.recalculate()
						});
						$("select").change(function(){
							if (this.name.indexOf('tyreDiameter') === 0) self.tyreDiameter = Number(this.value);
							else if (this.name.indexOf('chainringCount') === 0) self.chainringCount = Number(this.value);
							else if (this.name.indexOf('sprocketCount') === 0) self.sprocketCount = Number(this.value);
							else if (this.name.indexOf('wheelDiameter') === 0) self.wheelDiameter = Number(this.value);
							self.recalculate()
						});
						// this.setMyDefaults();
					},
					setMyDefaults: function() {
						// SET UP MY DEFAULT VALUES
						$('[name=wheelDiameter]').val(622).selectmenu("refresh", true);
						$('[name=tyreDiameter]').val(50).selectmenu("refresh", true);
						$('[name=targetCadence]').val(90);
						$('[name=sprocketCount]').val(9).selectmenu("refresh", true);
						$('[name=chainringCount]').val(3).selectmenu("refresh", true);
						var sprockets = [11,12,14,15,17,19,21,24,27]
						var chainrings = [50,39,30];
						this.chainringCount = 3;
						this.sprocketCount = 9;
						for (c = 1; c < 4; c++) $('[name=chainring'+c+']').val(chainrings[c-1]);
						for (g = 1; g < 10; g++) $('[name=gear'+g+']').val(sprockets[g-1]);
						this.recalculate();
					},
					recalculate: function() {
						this.updateChainrings();
						this.updateSprockets();
						this.wheelDiameter = Number($('[name=wheelDiameter]').val());
						this.tyreDiameter = Number($('[name=tyreDiameter]').val());
						this.targetCadence = Number($('[name=targetCadence]').val());
						if (this.chainrings.length && this.sprockets.length) {
							derailleurCapacity = Math.max.apply(Math, this.chainrings) - Math.min.apply(Math, this.chainrings) + Math.max.apply(Math, this.sprockets) - Math.min.apply(Math, this.sprockets);
							$('#derailleurCapacity').text(derailleurCapacity + ' ('+Math.max.apply(Math, this.chainrings)+'-'+Math.min.apply(Math, this.chainrings)+'+'+Math.max.apply(Math, this.sprockets)+'-'+Math.min.apply(Math, this.sprockets)+')');
							for (crank=0; crank < this.chainrings.length; crank++) {
								for (sprocket=0; sprocket < this.sprockets.length; sprocket++) {
									if (this.chainrings[crank] && this.sprockets[sprocket]) {
										var ratio = Number(this.chainrings[crank]/this.sprockets[sprocket]).toFixed(2);
										var output = 'Ratio='+ratio + ':1';
										if (this.wheelDiameter) {
											output += '<br />MD='+this.getUnitValue((((this.wheelDiameter + this.tyreDiameter) * 3.141 * ratio)/1000), 'm');
											output += '<br />ED='+this.getUnitValue(((this.wheelDiameter + this.tyreDiameter) * ratio), 'mm');
											output += '<br />GI='+(((this.wheelDiameter + this.tyreDiameter) / 25.4) * ratio).toFixed(0) + 'in';
											if (this.targetCadence) {
												var speed = (((this.wheelDiameter + this.tyreDiameter) * 3.141) * ratio * this.targetCadence * 60) / 1000000;
												output += '<br />'+this.getUnitValue(speed,'km/h')+'@'+this.targetCadence+'rpm';
											}
										}
										$('.chainring'+(crank+1) +'gear'+(sprocket+1)).css('background-color','hsl('+this.getHue(ratio)+', 100%, 50%)');
										$('.chainring'+(crank+1) +'gear'+(sprocket+1)).html('<div class="resultBox" style="background-color:hsl('+this.getHue(ratio)+', 100%, 50%);">'+output+'</div>');
									}
								}
							}
						}
					},
					getUnitValue: function(value, unit) {
						// RECEIVES A METRIC VALUE/UNIT & RETURNS IN IMPERIAL IF REQUIRED
						if ($('[name=unitToggler]:checked').val() == 'metric') return value.toFixed(2) + unit;
						if (unit == 'km/h') return (value * 0.621371192).toFixed(2) + 'mph';
						if (unit == 'm') return (value * 3.2808399).toFixed(2) + 'ft';
						if (unit == 'mm') return (value /25.4).toFixed(2) + 'in';
					},
					getHue: function(ratio) {
						return ((5 - ratio) * 20).toFixed(0);
						// return ((5 - ratio) * 50).toFixed(0);
					},
					updateChainrings: function() {
						var chainringCount = $('[name=chainringCount]').val();
						var chainrings = [];
						for (c = 1; c < this.chainringCount; c++) {
							var el = $('.chainringCol'+c);
							if (chainringCount < c) el.hide();
							else {
								el.show();
								chainrings.push(Number($('[name=chainring'+c+']').val()));
							}
						}
						this.chainrings = chainrings;
						this.chainringCount = chainringCount;
					},
					updateSprockets: function() {
						var sprocketCount = $('[name=sprocketCount]').val();
						var sprockets = [];
						for (c = 1; c < this.sprocketCount; c++) {
							var el = $('.chainringCol'+c);
							if (sprocketCount < c) el.hide();
							else {
								el.show();
								sprockets.push(Number($('[name=gear'+c+']').val()));
							}
						}
						this.sprockets = sprockets;
						this.sprocketCount = sprocketCount;
					},
					clearAll: function() {
						for (c = 1; c < 4; c++) $('[name=chainring'+c+']').val('');
						for (g = 1; g < 12; g++) $('[name=gear'+g+']').val('');
						this.chainrings = [];
						this.sprockets = [];
						$('[name=wheelDiameter]').val(0).selectmenu("refresh", true);
						this.wheelDiameter = 0;
						$('[name=chainringCount]').val(0).selectmenu("refresh", true);
						this.chainringCount = 0;
						$('[name=sprocketCount]').val(0).selectmenu("refresh", true);
						this.sprocketCount = 0;
						$('[name=tyreDiameter]').val(0).selectmenu("refresh", true);
						this.tyreDiameter = 0;
						$('[name=targetCadence]').val('');
						this.targetCadence = null;
						this.recalculate();
					}
				};
				
				
				gears.initialise()
				// setDefaults();
			});
/* 
			function updateSprocketCount() {
				var sprocketCount = $('[name=sprocketCount]').val();
				for (c = 1; c < 12; c++) {
					if (sprocketCount < c) $('.gearRow'+c).hide();
					else $('.gearRow'+c).show();
				}
			}
			
			function updateChainringCount() {
				var chainringCount = $('[name=chainringCount]').val();
				for (c = 1; c < 4; c++) {
					if (chainringCount < c) $('.chainringCol'+c).hide();
					else $('.chainringCol'+c).show();
				}
			}
			
			function setDefaults() {
				// SET UP MY DEFAULT VALUES
				$('[name=wheelDiameter]').val(622).selectmenu("refresh", true);
				$('[name=tyreDiameter]').val(50).selectmenu("refresh", true);
				$('[name=targetCadence]').val(90);
				var gears = [11,12,14,15,17,19,21,24,27]
				var chainrings = [50,39,30];
				for (c = 1; c < 4; c++) $('[name=chainring'+c+']').val(chainrings[c-1]);
				for (g = 1; g < 10; g++) $('[name=gear'+g+']').val(gears[g-1]);
				$('[name=sprocketCount]').val(9).selectmenu("refresh", true);
				$('[name=chainringCount]').val(3).selectmenu("refresh", true);
				recalculateAll();
			}
			
			function recalculateForChainring(el) {
				for (i = 1; i < 12; i++) {
					var chainring = Number(el.value);
					var gear = Number($('[name=gear'+i+']').val());
					var wheelDiameter = Number($('[name=wheelDiameter]').val());
					var tyreDiameter = Number($('[name=tyreDiameter]').val());
					var targetCadence = Number($('[name=targetCadence]').val());
					var units = $('[name=unitToggler]:checked').val();
					if (chainring && gear) {
						var output = 'Ratio='+(chainring/gear).toFixed(2) + ':1';
						if (wheelDiameter) {
							output += '<br />MD='+getUnitValue(((wheelDiameter * 3.141 * (chainring/gear))/1000), 'm');
							output += '<br />ED='+getUnitValue((wheelDiameter * (chainring/gear)), 'mm');
							output += '<br />GI='+(((wheelDiameter + tyreDiameter) / 25.4) * (chainring/gear)).toFixed(0) + 'in';
							if (targetCadence) {
								var speed = (((wheelDiameter + tyreDiameter) * 3.141) * (chainring/gear) * targetCadence * 60) / 1000000;
								output += '<br />'+getUnitValue(speed,'km/h')+'@'+targetCadence+'rpm';
							}
						}
						// COLOURS 4.55 TO 1.11 BUT LOWER SHOULD BE HIGHER
						// RATIO/-100 * 100
						// 4.55 / -100 = -95.45
						// 1.11 / -100 = 
						// $('.'+el.name+'gear'+i).html(output);
						var hue = ((5 - (chainring/gear)) * 20).toFixed(0);
						$('.'+el.name+'gear'+i).html('<div class="resultBox" style="background-color:hsl('+hue+', 100%, 50%);">'+output+'</div>');
					}
					else $('.'+el.name+'gear'+i).text('');
				}
			}
			
			function recalculateForSprocket(el) {
				for (i = 1; i < 4; i++) {
					var chainring = $('[name=chainring'+i+']').val();
					var gear = Number(el.value);
					var wheelDiameter = Number($('[name=wheelDiameter]').val());
					var tyreDiameter = Number($('[name=tyreDiameter]').val());
					var targetCadence = Number($('[name=targetCadence]').val());
					if (chainring && gear) {
						var output = 'Ratio='+(chainring/gear).toFixed(2) + ':1';
						if (wheelDiameter) {
							output += '<br />MD='+getUnitValue(((wheelDiameter * 3.141 * (chainring/gear))/1000), 'm');
							output += '<br />ED='+getUnitValue((wheelDiameter * (chainring/gear)), 'mm');
							output += '<br />GI='+(((wheelDiameter + tyreDiameter) / 25.4) * (chainring/gear)).toFixed(0) + 'in';
							if (targetCadence) {
								var speed = (((wheelDiameter + tyreDiameter) * 3.141) * (chainring/gear) * targetCadence * 60) / 1000000;
								output += '<br />'+getUnitValue(speed,'km/h')+'@'+targetCadence+'rpm';
							}
						}
						// $('.chainring'+i+el.name).html(output);
						var hue = ((5 - (chainring/gear)) * 20).toFixed(0);
						$('.chainring'+i+el.name).html('<div class="resultBox" style="background-color:hsl('+hue+', 100%, 50%);">'+output+'</div>');
					}
					else $('.chainring'+i+el.name).text('');
				}
			}
			
			function getUnitValue(value, unit) {
				// RECEIVES A METRIC VALUE/UNIT & RETURNS IN IMPERIAL IF REQUIRED
				if ($('[name=unitToggler]:checked').val() == 'metric') return value.toFixed(2) + unit;
				if (unit == 'km/h') return (value * 0.621371192).toFixed(2) + 'mph';
				if (unit == 'm') return (value * 3.2808399).toFixed(2) + 'ft';
				if (unit == 'mm') return (value /25.4).toFixed(2) + 'in';
			}
			
			function recalculateAll() {
				updateSprocketCount();
				updateChainringCount();
				for (c = 1; c < 4; c++) $('[name=chainring'+c+']').trigger('change');
				for (g = 1; g < 12; g++) $('[name=gear'+g+']').trigger('change');
			}
			
			function clearAll() {
				for (c = 1; c < 4; c++) $('[name=chainring'+c+']').val('');
				for (g = 1; g < 12; g++) $('[name=gear'+g+']').val('');
				$('[name=wheelDiameter]').val('');
				$('[name=chainringCount]').val(0).selectmenu("refresh", true);
				$('[name=sprocketCount]').val(0).selectmenu("refresh", true);
				$('[name=tyreDiameter]').val(0).selectmenu("refresh", true);
				$('[name=targetCadence]').val('');
				recalculateAll()
			}
 */
		</script>
	</head>

	<body>
		<div data-role="page" id="resultPage">
			<div data-role="main" class="ui-content">
				<a href="#infoPage" class="ui-btn ui-btn-inline ui-icon-info ui-btn-icon-left ui-mini ui-shadow">Info</a>
				<a href="#settingsPage" class="ui-btn ui-btn-inline ui-icon-gear ui-btn-icon-left ui-mini ui-shadow">Settings</a>
				<button type="button" class="ui-btn ui-btn-inline ui-icon-recycle ui-btn-icon-left ui-mini ui-shadow" id="clearAll">Clear data</button>
				<button type="button" class="ui-btn ui-btn-inline ui-icon-recycle ui-btn-icon-left ui-mini ui-shadow" id="setMyDefaults">My settings</button>
				
				<div data-role="fieldcontain">
					<label for="chainringCount">Chainrings:</label>
					<select name="chainringCount" data-inline="true">
						<option value="0">Select</option>
						<?php for($x=1;$x<7;$x++) echo '<option value="'.$x.'"'.($preselect && $x==2 ? ' selected="selected"' : '').'>'.$x.'</option>'; ?>
					</select>
				</div>
				<div data-role="fieldcontain">
					<label for="sprocketCount">Sprockets:</label>
					<select name="sprocketCount" data-inline="true">
						<option value="0">Select</option>
						<?php for($x=1;$x<12;$x++) echo '<option value="'.$x.'"'.($preselect && $x==9 ? ' selected="selected"' : '').'>'.$x.'</option>'; ?>
					</select>
				</div>
				<div data-role="fieldcontain">
					<label for="wheelDiameter">Wheel Diameter (mm):</label>
					<!--<input name="wheelDiameter" placeholder="Wheel Diameter (mm)" type="number" />-->
					<select name="wheelDiameter" data-inline="true">
						<option value="0">Select</option>
						<option value="630">27inch (630mm)</option>
						<option value="622"<?php echo ($preselect ? ' selected="selected"' : ''); ?>>700c/29er (622mm)</option>
						<option value="584">650b/27.5 (584mm)</option>
						<option value="571">650c (571mm)</option>
						<option value="559">26inch (559mm-mtb)</option>
						<option value="547">24inch (547mm-S5)</option>
						<option value="540">24inch (540mm-E6)</option>
						<option value="520">24inch (520mm-Terry)</option>
						<option value="451">20inch (451mm-Recumbent)</option>
						<option value="419">20inch (419mm-Schwinn)</option>
						<option value="406">20inch (406mm-Recumbent)</option>
					</select>
				</div>
				<div data-role="fieldcontain">
					<label for="tyreDiameter">Tyre Diameter (mm):</label>
					<select name="tyreDiameter" data-inline="true">
						<option value="0">Select</option>
						<?php foreach(array('18c'=>36,'23c'=>46,'25c'=>50,'28c'=>56,'32c'=>64,'35c'=>70,'47c'=>94) as $tyre=>$diameter) echo '<option value="'.$diameter.'"'.($preselect && $diameter==50 ? ' selected="selected"' : '').'>'.$tyre.'</option>'; ?>
					</select>
				</div>
				<div data-role="fieldcontain">
					<label for="targetCadence">Target cadence (rpm):</label>
					<input data-inline="true" data-wrapper-class="cadenceInput" name="targetCadence" placeholder="Target cadence (rpm)" type="number"<?php echo ($preselect ? 'value="90"' : ''); ?> />
				</div>
				<!--<div class="unitSwitcher">
					<label for="unitToggler" class="ui-hidden-accessible">Units:</label>
					<input type="checkbox" data-role="flipswitch" name="unitToggler" id="unitToggler" data-on-text="Metric" data-off-text="Imperial" data-wrapper-class="custom-label-flipswitch">
					<input type="checkbox" data-role="flipswitch" name="flip-checkbox-2" id="flip-checkbox-2" data-on-text="Light" data-off-text="Dark" data-wrapper-class="custom-label-flipswitch">
					<select name="units" id="flip-1" data-role="slider" data-mini="true">
						<option value="metric">Metric</option>
						<option value="imperial">Imperial</option>
					</select>
				</div>-->
				<div data-role="fieldcontain">
					<label for="unitToggler">Units:</label>
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
						<input type="radio" name="unitToggler" id="metric" value="metric" checked="checked">
						<label for="metric">Metric</label>
						<input type="radio" name="unitToggler" id="imperial" value="imperial">
						<label for="imperial">Imperial</label>
					</fieldset>
				</div>
				
				<p class="derailleurCapacityText">Derailleur capacity required: <span id="derailleurCapacity"></span></p>
				
				<table class="ratioTable">
					<tr>
						<th></th>
						<?php
						for ($chainring=1;$chainring<6;$chainring++) echo '<th class="chainringCol'.$chainring.'"><input placeholder="Chainring '.$chainring.'"type="number" name="chainring'.$chainring.'" data-mini="true" /></th>';
						?>
					</tr>
					<?php
					for ($cassette=1;$cassette<15;$cassette++) {
						echo '<tr class="gearRow'.$cassette.'"><th><input placeholder="Gear '.$cassette.'"type="number" name="gear'.$cassette.'" data-mini="true" /></th><td class="chainringCol1 chainring1gear'.$cassette.'"></td><td class="chainringCol2 chainring2gear'.$cassette.'"></td><td class="chainringCol3 chainring3gear'.$cassette.'"></td></tr>';
					}
					?>
				</table>
			</div>
		</div>
		<div data-role="page" id="settingsPage">
			<div data-role="main" class="ui-content">
				<a href="#infoPage" class="ui-btn ui-btn-inline ui-icon-info ui-btn-icon-left ui-mini ui-shadow">Info</a>
				<a href="#resultPage" class="ui-btn ui-btn-inline ui-icon-grid ui-btn-icon-left ui-mini ui-shadow">Results</a>
			</div>
		</div>
		<div data-role="page" id="infoPage">
			<div data-role="main" class="ui-content">
				<a href="#settingsPage" class="ui-btn ui-btn-inline ui-icon-gear ui-btn-icon-left ui-mini ui-shadow">Settings</a>
				<a href="#resultPage" class="ui-btn ui-btn-inline ui-icon-grid ui-btn-icon-left ui-mini ui-shadow">Results</a>
				<h1>Gear Ratio Calculator</h1>
				<p>Use this tool to calculate info about your gear ratios. Just enter the tooth count of your chainring/s and gear/s to see the gear ratios available.</p>
				<p>ED - Effective Diameter describes the equivalent diameter that the wheel would be with this gear ratio if the pedals were fixed directly to the wheel</p>
				<p>MD - Metres of Development (aka <a target="_blank" href="https://en.wikipedia.org/wiki/Gear_inches#Relationship_to_metres_of_development">Relationship to metres of development</a>)  describe the distance travelled for each pedal revolution</p>
				<p>GI - Gear Inches describes gear ratios in terms of the diameter of an equivalent directly driven wheel</p>
				<p>Speed/cadence - optionally add a target cadence to see the speed each gear will achieve at that cadence</p>
				<p>Derailleur capacity - yep, this tool can even tell you whet capacity derailleur you should be looking for based upon your crank &amp; cassette. The formula to calculate it is <i>(largest sprocket - smallest sprocket) + (largest chainring - smallest chainring)</i></p>
			</div>
		</div>
	</body>
</html>git status