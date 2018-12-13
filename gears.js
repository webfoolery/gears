/*
TODO::
	CREATE PRESET SELECTOR THAT AUTO POPULATES FROM THE presets OBJECT. THIS WILL ENABLE ADDING PRESETS BY CREATING NEW OBJECTS WITHOUT HAVING TO EDIT THE <select>
	TOGGLE CLASS ON RESULT TABLE FOR COLOURS INSTEAD OF ADDING INLINE CSS TO <td>
*/
			jQuery(document).ready(function($){
				var gears = {
					debug: false,
					// debug: (window.location.hash == 'debug' ? true : false),
					chainringCount: 0,
					sprocketCount: 0,
					chainrings: [],
					sprockets: [],
					wheelDiameter: 0,
					tyreDiameter: 0,
					crankLength: 0,
					targetCadence: 0,
					units: 'metric',
					presets: {
						standard09: {
							label: 'Standard chainset (53,39)', 
							chainrings: [53,39], 
							sprockets: [12,13,14,15,17,19,21,23,25], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 170, 
							targetCadence: 90, 
							name: '9sp standard'
						},
						standard10: {
							label: 'Standard chainset (53,39)', 
							chainrings: [53,39], 
							sprockets: [12,13,14,15,16,17,19,21,23,25], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 170, 
							targetCadence: 90, 
							name: '10sp standard'
						},
						standard11: {
							label: 'Standard chainset (53,39)', 
							chainrings: [53,39], 
							sprockets: [11,12,13,14,15,17,19,21,23,25,28], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 170, 
							targetCadence: 90, 
							name: '11sp standard'
						},
						compact09: {
							label: 'Compact chainset (50,34)', 
							chainrings: [50,34], 
							sprockets: [12,13,14,15,17,19,21,23,25], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 170, 
							targetCadence: 90, 
							name: '9sp compact'
						},
						compact10: {
							label: 'Compact chainset (50,34)', 
							chainrings: [50,34], 
							sprockets: [12,13,14,15,16,17,19,21,23,25], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 170, 
							targetCadence: 90, 
							name: '10sp compact'
						},
						compact11: {
							label: 'Compact chainset (50,34)', 
							chainrings: [50,34], 
							sprockets: [11,12,13,14,15,17,19,21,23,25,28], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 170, 
							targetCadence: 90, 
							name: '11sp compact'
						},
						triple09: {
							label: 'Triple chainset (50,39,30)', 
							chainrings: [50,39,30], 
							sprockets: [12,13,14,15,17,19,21,23,25], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 170, 
							targetCadence: 90, 
							name: '9sp triple'
						}, 
						csAttain: {
							label: 'Specific bikes', 
							chainrings: [50,34], 
							sprockets: [11,12,13,14,16,18,20,22,25,28,32], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 172.5, 
							targetCadence: 90, 
							name: 'Cube Attain GTC Race Disc (2018)'
						},
						csAgree: {
							label: 'Specific bikes', 
							chainrings: [50,34], 
							sprockets: [11,12,13,14,15,17,19,21,23,25,28], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 172.5, 
							targetCadence: 90, 
							name: 'Cube Agree C:62 Disc (2017)'
						},
						csPeloton: {
							label: 'Specific bikes', 
							chainrings: [50,39,30], 
							sprockets: [11,12,14,15,17,19,21,24,27], 
							wheelDiameter: 622, 
							tyreDiameter: 50, 
							crankLength: 170, 
							targetCadence: 90, 
							name: 'Cube Peloton (2011)'
						},
					}, 
					varDump: function(dumpData, title) {
						console.log(title, (dumpData ? JSON.parse(JSON.stringify(this)) : ''));
					},
					initialise: function() {
						var self = this;
						// console.log(self.presets.length);
						// SET UP THE PRESET LIST
						var presetSelector = document.getElementsByName('presets')[0];
						var labels = [];
						for (var key in self.presets) {
							// console.log('Key:'+key+' = Val: '+self.presets[key]);
							if (labels.indexOf(self.presets[key].label) == -1) {
								var newLabel = document.createElement('OPTGROUP')
								newLabel.label = self.presets[key].label;
								presetSelector.appendChild(newLabel);
								labels.push(self.presets[key].label);
							}
							presetSelector.add(new Option(self.presets[key].name, key));
						}
						this.resetTable();
						$("#clearAll").click(function(){self.clearAll()});
						$("input").change(function(){
							if (self.debug) self.varDump(false, 'Input changed');
							self.recalculate();
						});
						$("select").change(function(){
							if (this.name.indexOf('preset') === 0) return self.usePreset(this.value);
							if (self.debug) self.varDump(false, 'Select changed');
							self.recalculate();
						});
					},
					usePreset: function(selectedPreset) {
						if (!selectedPreset) selectedPreset = 'csPeloton';
						var self = this;
						this.clearAll();
						$('[name=wheelDiameter]').val(self.presets[selectedPreset]['wheelDiameter']).selectmenu("refresh", true);
						$('[name=tyreDiameter]').val(self.presets[selectedPreset]['tyreDiameter']).selectmenu("refresh", true);
						$('[name=targetCadence]').val(self.presets[selectedPreset]['targetCadence']);
						$('[name=crankLength]').val(self.presets[selectedPreset]['crankLength']);
						for (var c = 1; c <= self.presets[selectedPreset]['chainrings'].length; c++) $('[name=chainring'+c+']').val(self.presets[selectedPreset]['chainrings'][c-1]);
						for (var s = 1; s <= self.presets[selectedPreset]['sprockets'].length; s++) $('[name=sprocket'+s+']').val(self.presets[selectedPreset]['sprockets'][s-1]);
						this.recalculate();
					},
					recalculate: function() {
						this.resetTable();
						this.getChainrings();
						this.getSprockets();
						this.wheelDiameter = Number($('[name=wheelDiameter]').val());
						this.tyreDiameter = Number($('[name=tyreDiameter]').val());
						this.targetCadence = Number($('[name=targetCadence]').val());
						this.crankLength = Number($('[name=crankLength]').val());
						if (this.chainrings.length && this.sprockets.length) {
							for (var crank=0; crank < this.chainrings.length; crank++) {
								$('th.chainring'+(crank +1)).text(this.chainrings[crank]).show();
								for (var sprocket=0; sprocket < this.sprockets.length; sprocket++) {
									$('.sprocket'+(sprocket +1)).show();
									$('th.sprocket'+(sprocket +1)).text(this.sprockets[sprocket]);
									$('.chainring'+(crank +1) + 'sprocket'+(sprocket +1)).show();
									if (this.chainrings[crank] && this.sprockets[sprocket]) {
										// RATIO = CHAINRING TOOTHCOUNT / SPROCKET TOOTHCOUNT : 1
										var ratio = Number(this.chainrings[crank]/this.sprockets[sprocket]).toFixed(2);
										var output = 'Ratio='+ratio + ':1';
										if (this.wheelDiameter) {
											// METRES DEVELOPMENT = (WHEEL DIAMETER + TYRE DIAMETER) * PI * RATIO
											output += '<br />MD='+this.getUnitValue((((this.wheelDiameter + this.tyreDiameter) * 3.141 * ratio)/1000), 'm');
											// GEAR INCHES = (WHEEL + TYRE DIAMETER IN INCHES) * (CHAINRING TOOTHCOUNT / SPROCKET TOOTHCOUNT)
											output += '<br />GI='+this.getUnitValue(((this.wheelDiameter + this.tyreDiameter) * ratio)/10, 'cm');
											if (this.crankLength) {
												//  GAIN RATIO = ((WHEEL + TYRE RADIUS) / CRANK LENGTH) * GEAR RATIO
												var gr = ((((this.wheelDiameter + this.tyreDiameter) / 2) / this.crankLength) * ratio).toFixed(2);
												output += '<br />GR='+gr;
											}
											if (this.targetCadence) {
												// SPEED = ((WHEEL DIAMETER + TYRE DIAMETER) * PI) * RATIO * CADENCE * 60
												var speed = (((this.wheelDiameter + this.tyreDiameter) * 3.141) * ratio * this.targetCadence * 60) / 1000000;
												output += '<br />'+this.targetCadence+'rpm='+this.getUnitValue(speed,'km/h');
											}
										}
										if ($('[name=colourToggler]:checked').val() == 1) $('.chainring'+(crank+1) +'sprocket'+(sprocket+1)).css('background-color','hsl('+this.getHue(ratio)+', 100%, 50%)');
										else $('.chainring'+(crank+1) +'sprocket'+(sprocket+1)).css('background-color','transparent');
										$('.chainring'+(crank+1) +'sprocket'+(sprocket+1)).html(output);
										
										if (this.sprockets.length >=2) {
											var derailleurCapacityVal = Math.max.apply(Math, this.chainrings) - Math.min.apply(Math, this.chainrings) + Math.max.apply(Math, this.sprockets) - Math.min.apply(Math, this.sprockets);
											var derailleurCapacityText = '('+Math.max.apply(Math, this.chainrings)+'-'+Math.min.apply(Math, this.chainrings)+')+('+Math.max.apply(Math, this.sprockets)+'-'+Math.min.apply(Math, this.sprockets)+') = '+derailleurCapacityVal;
											$('#derailleurCapacity').text('Derailleur capacity: '+derailleurCapacityText).show();
										}
									}
								}
							}
							$('.resultTable.'+$('[name=orientationToggler]:checked').val()).show();
						}
						else {
							$('#derailleurCapacity').hide();
						}
						if (this.debug) this.varDump(true, 'recalculate complete');
					},
					getUnitValue: function(value, unit) {
						// RECEIVES A METRIC VALUE/UNIT & RETURNS IN IMPERIAL IF REQUIRED
						if ($('[name=unitToggler]:checked').val() == 'metric') return value.toFixed(2) + unit;
						if (unit == 'km/h') return (value * 0.621371192).toFixed(2) + 'mph';
						if (unit == 'm') return (value * 3.2808399).toFixed(2) + 'ft';
						if (unit == 'mm') return (value /25.4).toFixed(2) + 'in';
						if (unit == 'cm') return (value /2.54).toFixed(2) + 'in';
					},
					getHue: function(ratio) {
						return ((5 - ratio) * 20).toFixed(0);
					},
					getChainrings: function() {
						var chainringsTemp = [];
						$('.chainrings').each(function(el){
							var chainring = Number($(this).val());
							if (chainring) chainringsTemp.push(chainring);
						});
						this.chainrings = chainringsTemp;
						this.chainringCount = chainringsTemp.length;
						if (this.debug) this.varDump(true, 'getChainrings complete');
					},
					getSprockets: function() {
						var self = this;
						this.sprockets = [];
						$('.sprockets').each(function(el){
							var sprocket = Number($(this).val());
							if (sprocket) self.sprockets.push(sprocket);
						});
						this.sprocketCount = this.sprockets.length;
						if (this.debug) this.varDump(true, 'getsprockets complete');
					},
					clearAll: function() {
						this.resetTable();
						$('.result').html('').css('background-color', 'transparent');
						$('input.chainrings').val('');
						$('input.sprockets').val('');
						$('[name=wheelDiameter]').val(0).selectmenu("refresh", true);
						$('[name=chainringCount]').val(0);
						$('[name=sprocketCount]').val(0);
						$('[name=tyreDiameter]').val(0).selectmenu("refresh", true);
						$('[name=crankLength]').val('');
						$('[name=targetCadence]').val('');
						// $('[name=presets]').val(0).selectmenu("refresh", true);
						if (this.debug) this.varDump(true, 'clearAll complete');
						this.recalculate();
					},
					resetTable: function() {
						$('.resultTable').hide();
						$('.chainringHeading').hide();
						$('.sprocketHeading').hide();
						$('.result').hide();
					}
				};
				gears.initialise();
			});