Feature: Admin

    Background:
      Given I am logged in as Administrator

    Scenario: Back-office
      When I am on "/"
      Then I should see "Welcome to NMM Backoffice" in the "#main-container h1" element
