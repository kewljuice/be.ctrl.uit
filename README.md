# be.ctrl.uit

## Introduction
CiviCRM UiT extension: Everything functionally related to UiT migration.

## Installation
- You can directly clone to your CiviCRM extension directory using<br>
```$ git clone https://github.com/kewljuice/be.ctrl.uit.git```

- You can also download a zip file, and extract in your extension directory<br>
```$ git clone https://github.com/kewljuice/be.ctrl.uit/archive/master.zip```

- Configure CiviCRM Extensions Directory which can be done from<br>
```"Administer -> System Settings -> Directories".```

- Configure Extension Resource URL which can be done from<br>
```"Administer -> System Settings -> Resource URLs".```

- The next step is enabling the extension which can be done from<br> 
```"Administer -> System Settings -> Manage CiviCRM Extensions".```

## Requirements

- PHP v7.0+
- CiviCRM 5.0

## Configuration

- Manage settings: **yoursite.org/civicrm/uit/settings**.
- Manage config: **yoursite.org/civicrm/uit/config**.

## Endpoints

### UitMigrate: status

```
$result = civicrm_api3('UitMigrate', 'status', array(
    'UitType' => "events",
));
```