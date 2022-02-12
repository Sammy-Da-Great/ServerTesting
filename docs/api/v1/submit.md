# submit.php

### Request Type: POST
### Response Type: JSON

## Form Data

| Form Data | Description | Example |
| -------  | -------  | ------- |
| App | Either `stand` or `pit`. Used to sort the incomming data into stand and pit scouting. | `stand` |
| Version | A verion string to be recorded with the data submitted. | `v2018.2.0` |
| ScouterName | The name of the person who recorded the data. | `Sean` |
| ScouterTeamNumber | The team number of the person who recorded the data. | `4450` |
| EventKey | The event key of the event that scouting data is being submitted for. | `2018demo` |
| TeamNumber | The team number of the team that scouting data is being submitted for. | `4450` |
| Pre_StartingPos |	Either `Left`, `Right`, or `Center` representing where a team placed their robot at the start of a match. Use the driver's perspective for determining left and right. | `Left` |
| Auto_CrossedBaseline | Either `Crossed` or `Did not cross` representing if the team's robot crossed the base line during the autonomous period. | `Crossed` |
| Auto_Notes | General or extra notes on the team's robot's autonomous performance. | `Was disrupted by an alliance member.` |
| Auto_PlaceSwitch | The number of power cubes placed on the switch during autonomous. | `3` |
| Auto_PlaceScale | The number of power cubes placed on the scale during autonomous. | `2` |
| Teleop_ScalePlace | The number of power cubes placed on the scale during the teleoperated period. | `2` |
| Teleop_SwitchPlace | The number of power cubes placed on the switch during the teleoperated period. | `6` |
| Teleop_ExchangeVisit | The number of times the team's robot visited the exchange. | `9` |
| Teleop_Notes | General or extra notes on the team's robot's teleoperated performance. | `Very quick cube grabber. Could not hold onto a cube though.` |
| Notes | (Used only for stand scouting.) General notes about the robot's performance. | `They were pretty fast. And worked well with their alliance` |
| Pre_NoShow | (Used only for stand scouting.) Either `Showed Up` or `No Show`. Marks if the team's robot did not show up for the match. | `Showed up` |
| MatchNumber | (Used only for stand scouting.) The match number of the match being scouted. | `6` |
| Teleop_BoostUsed | (Used only for stand scouting.) Either `Used` or `Unused`. Marks if the Boost powerup was used during the match by the alliance. | `Used` |
| Teleop_ForceUsed | (Used only for stand scouting.) Either `Used` or `Unused`. Marks if the Force powerup was used during the match by the alliance. | `Used` |
| Teleop_LevitateUsed | (Used only for stand scouting.) Either `Used` or `Unused`. Marks if the Levitate powerup was used during the match by the alliance. | `Used` |
| Post_Climb | (Used only for stand scouting.) Either `No Climb - Not Parked`, `No Climb - Parked`, `Failed Climb`, `Climbed Alone`, `Assisted by another Robot`, `Climbed + Assisted 1 Robot`, `Climbed + Assisted 2 Robots`, `No Climb + Assisted 1 Robot`, or `No Climb + Assisted 2 Robots`. Represents the climbing state of the robot at the end of the match. | `No Climb + Assisted 1 Robot` |
| DOF | (Used only for stand scouting.) Either `Did not die on field` or `Died on field`. Marks if the robot died on the field at any point. Usually indicated by the driver station's light blinking during a match. | `Did not die on field` |
| Teleop_ScaleDrop | (Used only for stand scouting.) The number of times a power cube was dropped while attempting to place it on the scale during the teleoperated period. Not unique per cube. | `3` |
| Teleop_SwitchDrop | (Used only for stand scouting.) The number of times a power cube was dropped while attempting to place it on the switch during the teleoperated period. Not unique per cube. | `2` |
| Auto_DropSwitch | (Used only for stand scouting.) The number of times a power cube was dropped while attempting to place it on the switch during the autonomous period. Not unique per cube. | `2` |
| Auto_DropScale | (Used only for stand scouting.) The number of times a power cube was dropped while attempting to place it on the scale during the autonomous period. Not unique per cube. | `2` |
| RobotNotes | (Used only for pit scouting.) General or extra notes about the robot. | `The robot looked very well-constructed.` |
| Teleop_Climb | (Used only for pit scouting.) Either `No Climb`, `Climbed Alone`, `Climbed + Assisted 1 Robot`, `Climbed + Assisted 2 Robots`, `No Climb + Assisted 1 Robot`, or `No Climb + Assisted 2 Robots`. Represented the usual climbing strategy of a team. | `No Climb` | 
| Strategy_PowerUp | (Used only for pit scouting.) The usual strategy of the team for using powerups. | `Levitate first, then while we're head, boost then force.` |
| Strategy_General | (Used only for pit scouting.) The usual strategy of the team. | `Control their switch as fast as possible. Be offensive.` |

## Response
A JSON version of data submitted.

## Example Response
```
{"App":"stand","Version":"v2018.2.0","ScouterName":"Test","ScouterTeamNumber":"4450","EventKey":"2018demo","TeamNumber":"4450","Pre_StartingPos":"Center","Auto_CrossedBaseline":"Crossed","Auto_Notes":" ","Auto_PlaceSwitch":"10","Auto_PlaceScale":"22","Teleop_ScalePlace":"11","Teleop_SwitchPlace":"11","Teleop_ExchangeVisit":"22","Teleop_Notes":" ","Notes":"Best team ever","Pre_NoShow":"Showed Up","MatchNumber":"1","Teleop_BoostUsed":"Used","Teleop_ForceUsed":"Used","Teleop_LevitateUsed":"Used","Post_Climb":"No Climb - Not Parked","DOF":"Did not die on field","Teleop_ScaleDrop":"0","Teleop_SwitchDrop":"0","Auto_DropSwitch":"0","Auto_DropScale":"0","NoAlliance":"N\/A"}
```
