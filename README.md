# eZ Platform Query Bundle

A Bundle for eZ Platform, the open-source Symfony based CMS, that provides extra features around Repository Queries.

## Installation

## Features

Most features involve QueryTypes, predefined QueryObjects that accept parameters, and return a named Query.

### List QueryTypes
This command can list registered QueryTypes, and display details for one in particular:

```shell
# php app/console bd:query:types
There are 2 registered QueryTypes:

- AppBundle:LatestContent
- AppBundle:Menu


# php app/console bd:query:types AppBundle:LatestContent
Class: "AppBundle\QueryType\LatestContentQueryType"
Service: "app.query_type.latest_content",
Parameters: location_id, limit
```

### Run a Query
This command runs a Query, built from a QueryType, and displays the results:

```
# php app/console bd:query:run AppBundle:LatestContent location_id:2 
3 result(s) found in 0.06 seconds:

[60] Top Stories (blog)
[58] Contact Us (contact_form)
[59] Projects (gallery)
```
