# exportData.php

### Request Type: GET
### Response Type: Zip

## Form Data

| Form Data | Description | Example |
| -------  | -------  | ------- |
| exportType | Either `eventData`, `teamData`, `teamAtEventData`, or `allData`. This represents the type of data you would like exported to a zip file. |  `eventData` |
| eventKey | _(Only used with export types `eventData` and `teamAtEventData`)_ The event key of the event you want data about. Available through The Blue Alliance or in [syncDownload.php](syncDownload.md) | `2018waell` |
| teamNumber | _(Only used with export types `teamData` and `teamAtEventData`)_ The team number of the team you want data about. | `4450` |

## Response
A Zip file containing the exported data.
