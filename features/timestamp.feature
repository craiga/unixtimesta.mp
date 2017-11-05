Feature: Showing human-readble versions of timestamps

  Scenario: Getting a human-readable version of a timestamp.
    Given the user visits /123456789
    Then the user sees 123456789
    And the user sees Thursday, 29 November 1973 21:33:09 UTC

  Scenario: Getting the timestamp for a date.
    Given the user visits /1978/3/31
    Then the user sees 31 March 1978
    And the user sees 260150400

  Scenario: Getting the timestamp for a date and time.
    Given the user visits /1978/3/31/15/45/22
    Then the user sees 31 March 1978 15:45:22
    And the user sees 260207122

  Scenario: Getting the timestamp for a date description.
    Given the user visits /wednesday
    Then the user sees Wednesday
