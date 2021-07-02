# Bike Gearing Calculator
Usage: on the calculator page you can enter cassette & chainring sizes, crank length, tyre & wheel diameter etc. Based upon the info you enter you will be able to see the results described below.

There are several presets saved with common gear configurations (and a couple of my bikes) which can be useful for a quick starting point. If you want to create more presets just add a new object to the 'presets' variable.

See it in action [here](https://webfoolery.github.io/gears/)

* **Ratio:** Gear Ratio describes the rotations of the output gear in relation to rotations from the input gear. A ratio of 3:1 would mean that the wheel would rotate 3 times for each rotation of the chainring.
* **MD:** Metres of Development ([wiki](https://en.wikipedia.org/wiki/Gear_inches#Relationship_to_metres_of_development)) describes the distance the bike will travel for each full pedal revolution.
The formula is (wheel diameter + tyre diameter) × π × gear ratio
* **GI:** Gear Inches ([wiki](https://en.wikipedia.org/wiki/Gear_inches)) also known as Effective Diameter, describes gear ratios in terms of the diameter of an equivalent directly driven wheel if the pedals were fixed to that wheel (like a Penny Farthing).
The formula to calculate it is (wheel + tyre diameter in inches) × (chainring toothcount ÷ sprocket toothcount)
* **GR:** Gain Ratio is a Sheldon Brown innovation. Traditional measurements (GI, MD etc.) don't allow for the dis/advantage of pedal arm (crank) length and also make easy comparison of different gearing tricky (46/16 is the same as 53/19 - if the crank lengths are the same) so Sheldon Brown proposed a gear measurement system called "[gain ratio](http://sheldonbrown.com/gain.html)". It describes the ratio of distance travelled by the bike relative to the radial distance moved of the pedals.
His formula is ((wheel + tyre radius) ÷ crank length) × gear ratio. The benefits of this include:
Given 2 identically geared/wheeled bikes you can see a numerical representation of the mechanical dis/advantage if they have different crank lengths.
It's [dimensionless](https://en.wikipedia.org/wiki/Dimensionless_quantity), so whether you supply the measurements in inches, mm or microns the resulting value is the same.
It's like a universal language for comparing gearing, all reduced to a single number!
* **Speed/cadence:** Optionally add a target cadence to see the speed each gear will achieve at that cadence.
The formula is km/h = ((wheel diameter(mm) + tyre diameter(mm)) × π) × ratio × cadence × 60 / 100,000
* **Derailleur capacity:** This will tell you what capacity derailleur you should be looking for based upon your crank & cassette.
The formula to calculate it is (largest sprocket - smallest sprocket) + (largest chainring - smallest chainring)
* **Ratio list:** Underneath the results table you will see a list of all of the gear combination ratios in order, with any that are close to each other highlighted.

## Changelog
**2021-07-02**  
 - rel="noreferrer" added to external links
 - Javascript links moved to bottom of `<body>`
 - Updates README.md

**2021-06-29**  
 - Removes JQuery & JQuery Mobile styling
 - All javascript is now vanilla, some ES6, rewritten to improve efficiency
 - Adds ordering option to the results, ordering by gear ratio ascending or descending
 - Adds a list showing gear ratios in linear order and highlights those that are close together

 **2019-09-13**  
 - Adds (i) info icons to the form labels 

 **2018-12-13**  
 - Removes HTML for the preset selector and modified Javascript to populate the <options> from the preset object 

 **2018-12-11**  
 - Updates & adds some bike names 
  
 **2018-03-31**  
 - Removes dependancy on PHP, how operates solely as HTML, JS & CSS  
 - Removes text shadow on results table as it makes it difficult to read in some circumstances  
