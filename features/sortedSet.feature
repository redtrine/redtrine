Feature: Retrine Sorted Sets
  In order to Check Sorted Set abstraction
  As a Developer
  I need to test all valid methods

  Scenario Outline: Populating Sorted Set
    Given I have a working Redis Client
    And I have a sorted set called "MySortedSet"
    And I have a user with <userId>
    And the user has a score of <score>
    When I add an element with score
    Then the sorted has <total> items
    And the Sorted Set contains <userId>
    And has a score of <score>

    Examples:
      | userId |  score  | total |
      |  1001  |  1000   |  1    |
      |  1002  |  2000   |  2    |
      |  1003  |  3000   |  3    |
      |  1004  |  4000   |  4    |
      |  1005  |  5000   |  5    |
      |  1006  |  6000   |  6    |

  Scenario: Ranking
    Given I have a working Redis Client
    And I have a sorted set called "MySortedSet"
    And the sorted has "6" items
    When I get the highest Score
    Then I get userId "1006"
    And has a score of "6000"
    When I get the Lowest Score
    Then I get userId "1001"
    And has a score of "1000"

  Scenario: Removing items on Sorted Set
    Given I have a working Redis Client
    And I have a sorted set called "MySortedSet"
    And the sorted has "6" items
    When I remove user with id "1006"
    Then the sorted has "5" items
    When I get the highest Score
    Then I get userId "1005"
    And has a score of "5000"
