# Trello Dashboard
This tool uses the Trello API to pull statistical data about an organization's use of Trello. The statistical data is displayed in a dashboard. 

The following statistics are provided by this Trello Dashboard. Eeverything below is boards, members and actions belonging to the organization. xx is used in this desription instead of the numbers and percentages given by the tool.

```

Total boards = xx
Total members = xx
Total members who joined atleast 1 board = xx (xx %)
Total members who performed atleast 1 action = xx (xx %)
Total members who performed atleast 1 action within the last 2 months = xx (xx %)

List of boards

Board 1:
- xx members in board
- xx Total Actions

Board 2:
- xx members in board
- xx Total Actions

[And so on for the rest of the boards belonging to the organization.]

Organisation monthly breakdown:

September 2018

Number of people added to the organization in September : xx
Out of the xx members added this month,
how many joined at least 1 board:
- Within 1 week of being added: xx (xx%)
- Between 1 and 2 weeks of being added: xx (xx%)
- Between 2 and 3 weeks of being added: xx (xx%)
- Between 3 and 4 weeks of being added: xx (xx%)
- More than 4 weeks after being added: xx (xx%)
- Have never joined a board xx (xx%)

Out of the xx members added this month,
how many people performed actions (for all time):
- 0 actions: xx (xx%)
- At least 1 action: xx (xx%)
- At least 3 actions: xx (xx%)
- At least 5 actions: xx (xx%)
- At least 10 actions: xx (xx%)
- At least 20 actions: xx (xx%)

Number of actions within each board this month:
Board 1 (xx actions)
Board 2 (xx actions)
(And so on for the rest of the boards.)

Members who were added to the organization in September:
- @member1
  * Board 1: Performed xx actions
- @member2
  * Board 2: Performed xx actions


August 2018

[And so on for each previous month, till the beginning of the organization's use of Trello.]

```

## Status of Development

A lot of work has been done already and the dashboard shows lots of useful data. However, there may be some bugs, and also there are hardcoded values that have to be changed if other organizations want to use this tool. All should be easy changes.

## Technical Details

A rebuild tool is found in /rebuild/ and this tool is only ever needed if the json files are either lost or corrupt, or if a hidden board with more than 1000 actions is un hidden.

A refresh of the data via the cron tab twice daily is recommended, but can be done as many times as you wish. If time elapses and more than 1000 actions have happened since the last refresh, you will need to ue the rebuild tool.





