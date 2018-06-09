# Bike Gearing Calculator
Usage: on the calculator page you can enter cassette & chainring sizes, crank length, tyre & wheel diameter etc. Based upon the info you enter you will be able to see the results described below.

There are several presets saved with common gear configurations (and a couple of my bikes) which can be useful for a quick starting point. If you want to create more presets just add a new object to the 'presets' variable.

See it in action [here](https://webfoolery.github.io/gears/)

* **Ratio:** Gear Ratio describes the rotations of the output gear in relation to rotations from the input gear. A ratio of 3:1 would mean that the wheel would rotate 3 times for each rotation of the chainring.
* **MD:** Metres of Development (wiki) describes the distance the bike will travel for each full pedal revolution.
The formula is (wheel diameter + tyre diameter) × π × gear ratio
* **GI:** Gear Inches (wiki) also known as Effective Diameter, describes gear ratios in terms of the diameter of an equivalent directly driven wheel if the pedals were fixed to that wheel (like a Penny Farthing).
The formula to calculate it is (wheel + tyre diameter in inches) × (chainring toothcount ÷ sprocket toothcount)
* **GR:** Gain Ratio is a Sheldon Brown innovation. Traditional measurements (GI, MD etc.) don't allow for the dis/advantage of pedal arm (crank) length and also make easy comparison of different gearing tricky (46/16 is the same as 53/19 - if the crank lengths are the same) so Sheldon Brown proposed a gear measurement system called "gain ratio". It describes the ratio of distance travelled by the bike relative to the radial distance moved of the pedals.
His formula is ((wheel + tyre radius) ÷ crank length) × gear ratio. The benefits of this include:
Given 2 identically geared/wheeled bikes you can see a numerical representation of the mechanical dis/advantage if they have different crank lengths.
It's dimensionless, so whether you supply the measurements in inches, mm or microns the resulting value is the same.
It's like a universal language for comparing gearing, all reduced to a single number!
* **Speed/cadence:** Optionally add a target cadence to see the speed each gear will achieve at that cadence.
The formula is km/h = ((wheel diameter(mm) + tyre diameter(mm)) × π) × ratio × cadence × 60 / 100,000
* **Derailleur capacity:** This will tell you what capacity derailleur you should be looking for based upon your crank & cassette.
The formula to calculate it is (largest sprocket - smallest sprocket) + (largest chainring - smallest chainring)
