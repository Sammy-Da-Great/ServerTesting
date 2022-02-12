# syncDownload.php

### Request Type: GET
### Response Type: JSON

## Response 
A JSON file containing information on events that happen in 1 week or within the past 3 days, team lists of the teams at each event, and each match for each team at each event.

## Example Response

```json
{
    "CurrentVersion":
    [
        "2018",
        "2",
        "0"
    ],
    "Events":
    [
        {
            "city": "Example",
            "country": "US",
            "district":
            {
                "abbreviation": "PNW",
                "display_name": "Pacific Northwest",
                "key": "2018pnw",
                "year": 2018
            },
            "end_date": "2019-01-01",
            "event_code": "demo",
            "event_type": 0,
            "key": "2018demo",
            "name": "Demo Event",
            "start_date": "2018-01-01",
            "state_prov": "WA",
            "year": 2018
        }
    ],
    "TeamsByEvent":
    [
        {
            "EventKey": "2018demo",
            "TeamList":
            [
                {
                    "key": "frc4450",
                    "team_number": 4450,
                    "nickname": "Olympia Robotics Federation",
                    "name": "Olympia Robotics Federation",
                    "city": "Olympia",
                    "state_prov": "WA",
                    "country": "US"
                }
            ]
        }
    ],
    "EventMatches":
    [
        {
            "EventKey": "2018demo",
            "TeamNumber": 4450,
            "Matches":
            [
                "2018demo_qm1",
                "2018demo_qm5",
                "2018demo_qm15"
            ]
        }
    ]
}
```