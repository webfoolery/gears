document.addEventListener("DOMContentLoaded", function() {
    let gears = {
        debug: false,
        // debug: (window.location.hash == 'debug' ? true : false),
        chainringCount: 0,
        sprocketCount: 0,
        chainrings: [],
        sprockets: [],
        ratioList: [],
        wheelDiameter: 0,
        tyreDiameter: 0,
        crankLength: 0,
        targetCadence: 0,
        presetSelector: document.querySelector('[name=presets]'),
        inputWheelDiameter: document.querySelector('[name=wheelDiameter]'),
        inputTyreDiameter: document.querySelector('[name=tyreDiameter]'),
        inputTargetCadence: document.querySelector('[name=targetCadence]'),
        inputCrankLength: document.querySelector('[name=crankLength]'),
        allResultTable: document.querySelectorAll('.resultTable'),
        allChainringHeading: document.querySelectorAll('.chainringHeading'),
        allSprocketHeading: document.querySelectorAll('.sprocketHeading'),
        allResult: document.querySelectorAll('.result'),
        units: 'metric',
        presets: {
            standard09: {
                label: 'Standard chainset (53,39)',
                chainrings: [53, 39],
                sprockets: [12, 13, 14, 15, 17, 19, 21, 23, 25],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 170,
                targetCadence: 90,
                name: '9sp standard'
            },
            standard10: {
                label: 'Standard chainset (53,39)',
                chainrings: [53, 39],
                sprockets: [12, 13, 14, 15, 16, 17, 19, 21, 23, 25],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 170,
                targetCadence: 90,
                name: '10sp standard'
            },
            standard11: {
                label: 'Standard chainset (53,39)',
                chainrings: [53, 39],
                sprockets: [11, 12, 13, 14, 15, 17, 19, 21, 23, 25, 28],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 170,
                targetCadence: 90,
                name: '11sp standard'
            },
            compact09: {
                label: 'Compact chainset (50,34)',
                chainrings: [50, 34],
                sprockets: [12, 13, 14, 15, 17, 19, 21, 23, 25],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 170,
                targetCadence: 90,
                name: '9sp compact'
            },
            compact10: {
                label: 'Compact chainset (50,34)',
                chainrings: [50, 34],
                sprockets: [12, 13, 14, 15, 16, 17, 19, 21, 23, 25],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 170,
                targetCadence: 90,
                name: '10sp compact'
            },
            compact11: {
                label: 'Compact chainset (50,34)',
                chainrings: [50, 34],
                sprockets: [11, 12, 13, 14, 15, 17, 19, 21, 23, 25, 28],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 170,
                targetCadence: 90,
                name: '11sp compact'
            },
            triple09: {
                label: 'Triple chainset (50,39,30)',
                chainrings: [50, 39, 30],
                sprockets: [12, 13, 14, 15, 17, 19, 21, 23, 25],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 170,
                targetCadence: 90,
                name: '9sp triple'
            },
            csAttain: {
                label: 'Specific bikes',
                chainrings: [50, 34],
                sprockets: [11, 12, 13, 14, 16, 18, 20, 22, 25, 28, 32],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 172.5,
                targetCadence: 90,
                name: 'Cube Attain GTC Race Disc (2018)'
            },
            csAgree: {
                label: 'Specific bikes',
                chainrings: [50, 34],
                sprockets: [11, 12, 13, 14, 15, 17, 19, 21, 23, 25, 28],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 172.5,
                targetCadence: 90,
                name: 'Cube Agree C:62 Disc (2017)'
            },
            csAgreeMod: {
                label: 'Specific bikes',
                chainrings: [50, 34],
                sprockets: [11, 12, 13, 14, 16, 18, 20, 22, 25, 28, 32],
                wheelDiameter: 622,
                tyreDiameter: 50,
                crankLength: 172.5,
                targetCadence: 90,
                name: 'Cube Agree C:62 Disc (2017) 11-32 cassette'
            },
            csPeloton: {
                label: 'Specific bikes',
                chainrings: [50, 39, 30],
                sprockets: [11, 12, 14, 15, 17, 19, 21, 24, 27],
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
            // SET UP THE PRESET LIST
            let labels = [];
            for (let key in this.presets) {
                if (labels.indexOf(this.presets[key].label) == -1) {
                    let newLabel = document.createElement('OPTGROUP');
                    newLabel.label = this.presets[key].label;
                    this.presetSelector.appendChild(newLabel);
                    labels.push(this.presets[key].label);
                }
                this.presetSelector.add(new Option(this.presets[key].name, key));
            }
            this.resetTable();
            document.getElementById("clearAll").addEventListener('click', () => this.clearAll());
            document.querySelectorAll('input').forEach(el => {
                el.addEventListener('change', (e) => {
                    if (this.debug) this.varDump(false, 'Input changed');
                    this.recalculate();
                })
            });
            document.querySelectorAll('select').forEach(el => {
                el.addEventListener('change', (e) => {
                    if (e.target.name.indexOf('preset') === 0) return this.usePreset(el.value);
                    if (this.debug) this.varDump(false, 'Select changed');
                    this.recalculate();
                })
            });
        },
        usePreset: function(selectedPreset) {
            if (!selectedPreset) selectedPreset = 'csPeloton';
            this.clearAll();
            this.inputWheelDiameter.value = this.presets[selectedPreset]['wheelDiameter'];
            this.inputTyreDiameter.value = this.presets[selectedPreset]['tyreDiameter'];
            this.inputTargetCadence.value = this.presets[selectedPreset]['targetCadence'];
            this.inputCrankLength.value = this.presets[selectedPreset]['crankLength'];
            for (let c = 1; c <= this.presets[selectedPreset]['chainrings'].length; c++) {
                document.querySelector('[name=chainring' + c + ']').value = this.presets[selectedPreset]['chainrings'][c - 1];
            }
            for (let s = 1; s <= this.presets[selectedPreset]['sprockets'].length; s++) {
                document.querySelector('[name=sprocket' + s + ']').value = this.presets[selectedPreset]['sprockets'][s - 1];
            }
            this.recalculate();
        },
        recalculate: function() {
            this.resetTable();
            this.getChainrings();
            this.getSprockets();
            this.wheelDiameter = Number(this.inputWheelDiameter.value);
            this.tyreDiameter = Number(this.inputTyreDiameter.value);
            this.targetCadence = Number(this.inputTargetCadence.value);
            this.crankLength = Number(this.inputCrankLength.value);
            this.resultsOrder = document.querySelector('[name=resultsOrder]:checked').value;
            if (this.resultsOrder == 'ascending') {
                this.chainrings.reverse();
                this.sprockets.reverse();
            }
            if (this.chainrings.length && this.sprockets.length) {
                if (this.debug) console.table([this.chainrings, this.sprockets]);
                for (let crank = 0; crank < this.chainrings.length; crank++) {
                    document.querySelectorAll('th.chainring' + (crank + 1)).forEach(el => {
                        el.innerText = this.chainrings[crank];
                        el.style.display = '';
                    });
                    for (let sprocket = 0; sprocket < this.sprockets.length; sprocket++) {
                        document.querySelectorAll('th.sprocket' + (sprocket + 1)).forEach(el => {
                            el.style.display = '';
                            el.innerText = this.sprockets[sprocket];
                        });
                        document.querySelectorAll('.chainring' + (crank + 1) + 'sprocket' + (sprocket + 1)).forEach(el => el.style.display = '');
                        if (this.chainrings[crank] && this.sprockets[sprocket]) {
                            // RATIO = CHAINRING TOOTHCOUNT / SPROCKET TOOTHCOUNT : 1
                            let ratio = Number(this.chainrings[crank] / this.sprockets[sprocket]).toFixed(2);
                            this.ratioList.push({ 'chainring': this.chainrings[crank], 'sprocket': this.sprockets[sprocket], 'ratio': ratio })
                            let output = '<span title="Gear Ratio">Ratio</span>=' + ratio + ':1';
                            if (this.wheelDiameter) {
                                // METRES DEVELOPMENT = (WHEEL DIAMETER + TYRE DIAMETER) * PI * RATIO
                                output += '<br /><span title="Metres of Development">MD</span>=' + this.getUnitValue((((this.wheelDiameter + this.tyreDiameter) * 3.141 * ratio) / 1000), 'm');
                                // GEAR INCHES = (WHEEL + TYRE DIAMETER IN INCHES) * (CHAINRING TOOTHCOUNT / SPROCKET TOOTHCOUNT)
                                output += '<br /><span title="Gear inches">GI</span>=' + this.getUnitValue(((this.wheelDiameter + this.tyreDiameter) * ratio) / 10, 'cm');
                                if (this.crankLength) {
                                    //  GAIN RATIO = ((WHEEL + TYRE RADIUS) / CRANK LENGTH) * GEAR RATIO
                                    let gr = ((((this.wheelDiameter + this.tyreDiameter) / 2) / this.crankLength) * ratio).toFixed(2);
                                    output += '<br /><span title="Gain Ratio">GR</span>=' + gr;
                                }
                                if (this.targetCadence) {
                                    // SPEED = ((WHEEL DIAMETER + TYRE DIAMETER) * PI) * RATIO * CADENCE * 60
                                    let speed = (((this.wheelDiameter + this.tyreDiameter) * 3.141) * ratio * this.targetCadence * 60) / 1000000;
                                    output += '<br />@' + this.targetCadence + '<span title="Revolutions per minute">rpm</span>=' + this.getUnitValue(speed, 'km/h');
                                }
                            }
                            if (document.querySelector('[name=colourToggler]:checked').value == 1) document.querySelectorAll('.chainring' + (crank + 1) + 'sprocket' + (sprocket + 1)).forEach(resultCell => resultCell.style.backgroundColor = 'hsl(' + this.getHue(ratio) + ', 100%, 50%)');
                            else document.querySelectorAll('.chainring' + (crank + 1) + 'sprocket' + (sprocket + 1)).forEach(resultCell => resultCell.style.backgroundColor = '#eee');
                            document.querySelectorAll('.chainring' + (crank + 1) + 'sprocket' + (sprocket + 1)).forEach(el => el.innerHTML = output);

                            if (this.sprockets.length >= 2) {
                                let derailleurCapacityVal = Math.max.apply(Math, this.chainrings) - Math.min.apply(Math, this.chainrings) + Math.max.apply(Math, this.sprockets) - Math.min.apply(Math, this.sprockets);
                                let derailleurCapacityText = '(' + Math.max.apply(Math, this.chainrings) + '-' + Math.min.apply(Math, this.chainrings) + ')+(' + Math.max.apply(Math, this.sprockets) + '-' + Math.min.apply(Math, this.sprockets) + ') = ' + derailleurCapacityVal;
                                document.getElementById('derailleurCapacity').innerText = 'Derailleur capacity: ' + derailleurCapacityText;
                            }
                        }
                    }
                }
                document.querySelector('.resultTable.' + document.querySelector('[name=orientationToggler]:checked').value).style.display = '';
                // POPULATE THE RATIO LIST
                let theList = document.getElementById('resultList');
                this.ratioList.sort(function(a, b) {
                    return a.ratio - b.ratio;
                });
                this.ratioList.forEach((el, index) => {
                    let style = '';
                    let text = el.chainring + '/' + el.sprocket + ' = ' + el.ratio + ':1';
                    // COMPARE NEXT ONE
                    closePercent = 1.02;
                    if (typeof this.ratioList[index + 1] !== 'undefined') {
                        let nextRatio = this.ratioList[index + 1];
                        if ((this.ratioList[index + 1].ratio) <= (el.ratio * closePercent)) {
                            // style += 'color:red;';
                            text += ' is close to ' + nextRatio.chainring + '/' + nextRatio.sprocket + ' = ' + nextRatio.ratio + ':1';
                        }
                    }
                    // COMPARE PREVIOUS ONE
                    if (typeof this.ratioList[index - 1] !== 'undefined') {
                        let previousRatio = this.ratioList[index - 1];
                        if ((this.ratioList[index - 1].ratio * closePercent) >= el.ratio) {
                            // style += 'background:yellow;';
                            text += ' is close to ' + previousRatio.chainring + '/' + previousRatio.sprocket + ' = ' + previousRatio.ratio + ':1';
                        }
                    }
                    let li = document.createElement('li');
                    // li.style.cssText = style;
                    li.appendChild(document.createTextNode(text));
                    theList.appendChild(li);
                });
                document.getElementById('percentageNote').innerText = ((closePercent * 100) - 100);
                document.getElementById('resultListContainer').style.display = '';
            } else {}
            if (this.debug) this.varDump(true, 'recalculate complete');
        },
        getUnitValue: function(value, unit) {
            // RECEIVES A METRIC VALUE/UNIT & RETURNS IN IMPERIAL IF REQUIRED
            if (document.querySelector('[name=unitToggler]:checked').value == 'metric') return value.toFixed(2) + unit;
            if (unit == 'km/h') return (value * 0.621371192).toFixed(2) + 'mph';
            if (unit == 'm') return (value * 3.2808399).toFixed(2) + 'ft';
            if (unit == 'mm') return (value / 25.4).toFixed(2) + 'in';
            if (unit == 'cm') return (value / 2.54).toFixed(2) + 'in';
        },
        getHue: function(ratio) {
            return ((5 - ratio) * 20).toFixed(0);
        },
        getChainrings: function() {
            let chainringsTemp = [];
            document.querySelectorAll('.chainrings').forEach(el => {
                let chainringNumber = Number(el.value);
                if (chainringNumber) chainringsTemp.push(chainringNumber);
            })
            this.chainrings = chainringsTemp;
            this.chainringCount = chainringsTemp.length;
            if (this.debug) this.varDump(true, 'getChainrings complete');
        },
        getSprockets: function() {
            this.sprockets = [];
            document.querySelectorAll('.sprockets').forEach(el => {
                let sprocketNumber = Number(el.value);
                if (sprocketNumber) this.sprockets.push(sprocketNumber);
            })
            this.sprocketCount = this.sprockets.length;
            if (this.debug) this.varDump(true, 'getsprockets complete');
        },
        clearAll: function() {
            this.resetTable();
            document.querySelectorAll('.result').forEach(el => {
                el.innerHTML = '';
                el.style.backgroundColor = 'transparent';
            });
            document.querySelectorAll('input.chainrings, input.sprockets, [name=crankLength], [name=targetCadence]').forEach(item => item.value = '');
            document.querySelectorAll('[name=wheelDiameter], [name=chainringCount], [name=sprocketCount], [name=tyreDiameter]').forEach(item => item.value = 0);
            if (this.debug) this.varDump(true, 'clearAll complete');
            this.recalculate();
        },
        resetTable: function() {
            this.ratioList = [];
            document.getElementById('resultList').innerHTML = '';
            document.getElementById('resultListContainer').style.display = 'none';
            this.allResultTable.forEach(el => el.style.display = 'none');
            this.allChainringHeading.forEach(el => el.style.display = 'none');
            this.allSprocketHeading.forEach(el => el.style.display = 'none');
            this.allResult.forEach(el => el.style.display = 'none');
        }
    };
    gears.initialise();
});