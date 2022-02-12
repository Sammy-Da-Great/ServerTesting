# retieveTeam.php

### Request Type: GET
### Response Type: JSON

## Form Data

| Form Data | Description | Example |
| -------  | -------  | ------- |
| teamNumber | The team number of the team you want information on. |  `4450` |
| eventCode | The event key of the event you want data about. Available through The Blue Alliance or in [syncDownload.php](syncDownload.md) | `2018waell` |
| showHiddenData | A password used to reveal hidden data. (Nothing currently right now hidden.) | `password123` |

## Response
A JSON file containing the information on the team at the specified event.

## Example Response

```json
{
    "TeamNumber": "4450",
    "EventCode": "2018demo",
    "SeasonYear": "2018",
    "Pit":
    {
        "Pre_StartingPos": "Unknown",
        "Auto_CrossedBaseline": "Unknown",
        "Auto_Notes": "Unknown",
        "Auto_PlaceSwitch": "Unknown",
        "Auto_PlaceScale": "Unknown",
        "Teleop_ScalePlace": "Unknown",
        "Teleop_SwitchPlace": "Unknown",
        "Teleop_ExchangeVisit": "Unknown",
        "Teleop_Notes": "Unknown",
        "RobotNotes": "Unknown",
        "Teleop_Climb": "Unknown",
        "Strategy_PowerUp": "Unknown",
        "Strategy_General": "Unknown"
    },
    "Stand":
    {
        "Matches":
        {
            "1":
            [
                {
                    "App": "stand",
                    "Version": "v2018.2.0",
                    "ScouterName": "Test",
                    "ScouterTeamNumber": "4450",
                    "EventKey": "2018demo",
                    "TeamNumber": "4450",
                    "Pre_StartingPos": "Center",
                    "Auto_CrossedBaseline": "Crossed",
                    "Auto_Notes": " ",
                    "Auto_PlaceSwitch": "10",
                    "Auto_PlaceScale": "22",
                    "Teleop_ScalePlace": "11",
                    "Teleop_SwitchPlace": "11",
                    "Teleop_ExchangeVisit": "22",
                    "Teleop_Notes": " ",
                    "Notes": "Best team ever",
                    "Pre_NoShow": "Showed Up",
                    "MatchNumber": "1",
                    "Teleop_BoostUsed": "Used",
                    "Teleop_ForceUsed": "Used",
                    "Teleop_LevitateUsed": "Used",
                    "Post_Climb": "No Climb - Not Parked",
                    "DOF": "Did not die on field",
                    "Teleop_ScaleDrop": "0",
                    "Teleop_SwitchDrop": "0",
                    "Auto_DropSwitch": "0",
                    "Auto_DropScale": "0"
                },
                {
                    "App": "stand",
                    "Version": "v2018.2.0",
                    "ScouterName": "Holly",
                    "ScouterTeamNumber": "4450",
                    "EventKey": "2018demo",
                    "TeamNumber": "4450",
                    "Pre_StartingPos": "Center",
                    "Auto_CrossedBaseline": "Crossed",
                    "Auto_Notes": " ",
                    "Auto_PlaceSwitch": "3",
                    "Auto_PlaceScale": "5",
                    "Teleop_ScalePlace": "0",
                    "Teleop_SwitchPlace": "0",
                    "Teleop_ExchangeVisit": "5",
                    "Teleop_Notes": " ",
                    "Notes": "scouting test",
                    "Pre_NoShow": "Showed Up",
                    "MatchNumber": "1",
                    "Teleop_BoostUsed": "Used",
                    "Teleop_ForceUsed": "Used",
                    "Teleop_LevitateUsed": "Used",
                    "Post_Climb": "No Climb - Not Parked",
                    "DOF": "Did not die on field",
                    "Teleop_ScaleDrop": "3",
                    "Teleop_SwitchDrop": "0",
                    "Auto_DropSwitch": "3",
                    "Auto_DropScale": "3"
                },
                {
                    "App": "stand",
                    "Version": "v2018.2.0",
                    "ScouterName": "Test",
                    "ScouterTeamNumber": "4450",
                    "EventKey": "2018demo",
                    "TeamNumber": "4450",
                    "Pre_StartingPos": "Center",
                    "Auto_CrossedBaseline": "Crossed",
                    "Auto_Notes": " ",
                    "Auto_PlaceSwitch": "10",
                    "Auto_PlaceScale": "22",
                    "Teleop_ScalePlace": "11",
                    "Teleop_SwitchPlace": "11",
                    "Teleop_ExchangeVisit": "22",
                    "Teleop_Notes": " ",
                    "Notes": "Best team ever",
                    "Pre_NoShow": "Showed Up",
                    "MatchNumber": "1",
                    "Teleop_BoostUsed": "Used",
                    "Teleop_ForceUsed": "Used",
                    "Teleop_LevitateUsed": "Used",
                    "Post_Climb": "No Climb - Not Parked",
                    "DOF": "Did not die on field",
                    "Teleop_ScaleDrop": "0",
                    "Teleop_SwitchDrop": "0",
                    "Auto_DropSwitch": "0",
                    "Auto_DropScale": "0"
                }
            ],
            "5":
            [
                {
                    "App": "stand",
                    "Version": "v2018.2.0",
                    "ScouterName": "Sean",
                    "ScouterTeamNumber": "4450",
                    "EventKey": "2018demo",
                    "TeamNumber": "4450",
                    "Pre_StartingPos": "Left",
                    "Auto_CrossedBaseline": "Crossed",
                    "Auto_Notes": " ",
                    "Auto_PlaceSwitch": "2",
                    "Auto_PlaceScale": "0",
                    "Teleop_ScalePlace": "0",
                    "Teleop_SwitchPlace": "4",
                    "Teleop_ExchangeVisit": "9",
                    "Teleop_Notes": " ",
                    "Notes": "In general, worked really well with other teams.",
                    "Pre_NoShow": "Showed Up",
                    "MatchNumber": "5",
                    "Teleop_BoostUsed": "Used",
                    "Teleop_ForceUsed": "Used",
                    "Teleop_LevitateUsed": "Used",
                    "Post_Climb": "Climbed + Assisted 1 Robot",
                    "DOF": "Did not die on field",
                    "Teleop_ScaleDrop": "0",
                    "Teleop_SwitchDrop": "0",
                    "Auto_DropSwitch": "0",
                    "Auto_DropScale": "0"
                }
            ]
        },
        "AvgExchangeVisits": 14.5,
        "AvgSwitchPlaces": 12.75,
        "AvgScalePlaces": 17.75,
        "AvgSwitchDrops": 0.75,
        "AvgScaleDrops": 1.5
    },
    "EventName": "Demo Event",
    "TeamName": "Olympia Robotics Federation",
    "TeamStatusString": "Status Unavailable",
    "Media":
    {
        "0":
        {
            "details":
            {
                "base64Image": "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"
            },
            "foreign_key": "avatar_2018_frc4450",
            "preferred": false,
            "type": "avatar"
        },
        "1":
        {
            "details": [],
            "foreign_key": "OWZXY4M",
            "preferred": true,
            "type": "imgur"
        },
        "2":
        {
            "details": [],
            "foreign_key": "zUBNQm7",
            "preferred": true,
            "type": "imgur"
        },
        "Preferred": 1
    }
}
```
