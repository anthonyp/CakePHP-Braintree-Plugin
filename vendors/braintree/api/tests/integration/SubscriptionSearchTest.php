<?php
require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';
require_once realpath(dirname(__FILE__)) . '/SubscriptionTestHelper.php';

class Braintree_SubscriptionSearchTest extends PHPUnit_Framework_TestCase
{
    function testSearch_planIdIs()
    {
        $creditCard = Braintree_SubscriptionTestHelper::createCreditCard();
        $triallessPlan = Braintree_SubscriptionTestHelper::triallessPlan();
        $trialPlan = Braintree_SubscriptionTestHelper::trialPlan();

        $trialSubscription = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $trialPlan['id'],
            'price' => '1'
        ))->subscription;

        $triallessSubscription = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'price' => '1'
        ))->subscription;

        $collection = Braintree_Subscription::search(array(
            Braintree_SubscriptionSearch::planId()->is('integration_trial_plan'),
            Braintree_SubscriptionSearch::price()->is('1')
        ));

        $this->assertTrue(Braintree_TestHelper::includes($collection, $trialSubscription));
        $this->assertFalse(Braintree_TestHelper::includes($collection, $triallessSubscription));
    }

    function testSearch_statusIsPastDue()
    {
        $found = false;
        $collection = Braintree_Subscription::search(array(
            Braintree_SubscriptionSearch::status()->in(array(Braintree_Subscription::PAST_DUE))
        ));
        foreach ($collection AS $item) {
            $found = true;
            $this->assertEquals(Braintree_Subscription::PAST_DUE, $item->status);
        }
        $this->assertTrue($found);
    }

    function testSearch_statusIsExpired()
    {
        $found = false;
        $collection = Braintree_Subscription::search(array(
            Braintree_SubscriptionSearch::status()->in(array(Braintree_Subscription::EXPIRED))
        ));
        foreach ($collection AS $item) {
            $found = true;
            $this->assertEquals(Braintree_Subscription::EXPIRED, $item->status);
        }
        $this->assertTrue($found);
    }

    function testSearch_billingCyclesRemaing()
    {
        $creditCard = Braintree_SubscriptionTestHelper::createCreditCard();
        $triallessPlan = Braintree_SubscriptionTestHelper::triallessPlan();

        $subscription_4 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'numberOfBillingCycles' => 4
        ))->subscription;

        $subscription_8 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'numberOfBillingCycles' => 8
        ))->subscription;

        $subscription_10 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'numberOfBillingCycles' => 10
        ))->subscription;

        $collection = Braintree_Subscription::search(array(
            Braintree_SubscriptionSearch::billingCyclesRemaining()->between(5, 10)
        ));

        $this->assertFalse(Braintree_TestHelper::includes($collection, $subscription_4));
        $this->assertTrue(Braintree_TestHelper::includes($collection, $subscription_8));
        $this->assertTrue(Braintree_TestHelper::includes($collection, $subscription_10));
    }

    function testSearch_subscriptionId()
    {
        $creditCard = Braintree_SubscriptionTestHelper::createCreditCard();
        $triallessPlan = Braintree_SubscriptionTestHelper::triallessPlan();

        $rand_id = strval(rand());

        $subscription_1 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'id' => 'subscription_123_id_' . $rand_id
        ))->subscription;

        $subscription_2 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'id' => 'subscription_23_id_' . $rand_id
        ))->subscription;

        $subscription_3 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'id' => 'subscription_3_id_' . $rand_id
        ))->subscription;

        $collection = Braintree_Subscription::search(array(
            Braintree_SubscriptionSearch::id()->contains("23_id_")
        ));

        $this->assertTrue(Braintree_TestHelper::includes($collection, $subscription_1));
        $this->assertTrue(Braintree_TestHelper::includes($collection, $subscription_2));
        $this->assertFalse(Braintree_TestHelper::includes($collection, $subscription_3));
    }

    function testSearch_merchantAccountId()
    {
        $creditCard = Braintree_SubscriptionTestHelper::createCreditCard();
        $triallessPlan = Braintree_SubscriptionTestHelper::triallessPlan();

        $rand_id = strval(rand());

        $subscription_1 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'id' => strval(rand()) . '_subscription_' . $rand_id,
            'price' => '2'
        ))->subscription;

        $subscription_2 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'id' => strval(rand()) . '_subscription_' . $rand_id,
            'merchantAccountId' => Braintree_TestHelper::nonDefaultMerchantAccountId(),
            'price' => '2'
        ))->subscription;

        $collection = Braintree_Subscription::search(array(
            Braintree_SubscriptionSearch::id()->endsWith('subscription_' . $rand_id),
            Braintree_SubscriptionSearch::merchantAccountId()->in(array(Braintree_TestHelper::nonDefaultMerchantAccountId())),
            Braintree_SubscriptionSearch::price()->is('2')
        ));

        $this->assertFalse(Braintree_TestHelper::includes($collection, $subscription_1));
        $this->assertTrue(Braintree_TestHelper::includes($collection, $subscription_2));
    }

    function testSearch_daysPastDue()
    {
        $creditCard = Braintree_SubscriptionTestHelper::createCreditCard();
        $triallessPlan = Braintree_SubscriptionTestHelper::triallessPlan();

        $subscription = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id']
        ))->subscription;

        Braintree_Http::put('/subscriptions/' . $subscription->id . '/make_past_due', array('daysPastDue' => 5));

        $found = false;
        $collection = Braintree_Subscription::search(array(
            Braintree_SubscriptionSearch::daysPastDue()->between(2, 10)
        ));
        foreach ($collection AS $item) {
            $found = true;
            $this->assertTrue($item->daysPastDue <= 10);
            $this->assertTrue($item->daysPastDue >= 2);
        }
        $this->assertTrue($found);
    }

    function testSearch_price()
    {
        $creditCard = Braintree_SubscriptionTestHelper::createCreditCard();
        $triallessPlan = Braintree_SubscriptionTestHelper::triallessPlan();

        $subscription_850 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'price' => '8.50'
        ))->subscription;

        $subscription_851 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'price' => '8.51'
        ))->subscription;

        $subscription_852 = Braintree_Subscription::create(array(
            'paymentMethodToken' => $creditCard->token,
            'planId' => $triallessPlan['id'],
            'price' => '8.52'
        ))->subscription;

        $collection = Braintree_Subscription::search(array(
            Braintree_SubscriptionSearch::price()->between('8.51', '8.52')
        ));

        $this->assertTrue(Braintree_TestHelper::includes($collection, $subscription_851));
        $this->assertTrue(Braintree_TestHelper::includes($collection, $subscription_852));
        $this->assertFalse(Braintree_TestHelper::includes($collection, $subscription_850));
    }
}
