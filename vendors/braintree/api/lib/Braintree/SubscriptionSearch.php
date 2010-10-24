<?php
class Braintree_SubscriptionSearch
{
    static function billingCyclesRemaining()
    {
        return new Braintree_RangeNode('billing_cycles_remaining');
    }

    static function daysPastDue()
    {
        return new Braintree_RangeNode('days_past_due');
    }

    static function id()
    {
        return new Braintree_TextNode('id');
    }

    static function merchantAccountId()
    {
        return new Braintree_MultipleValueNode('merchant_account_id');
    }

    static function planId()
    {
        return new Braintree_MultipleValueOrTextNode('plan_id');
    }

    static function price()
    {
        return new Braintree_RangeNode('price');
    }

    static function status()
    {
        return new Braintree_MultipleValueNode("status", array(
            Braintree_Subscription::ACTIVE,
            Braintree_Subscription::CANCELED,
            Braintree_Subscription::EXPIRED,
            Braintree_Subscription::PAST_DUE,
            Braintree_Subscription::PENDING
        ));
    }

    static function ids()
    {
        return new Braintree_MultipleValueNode('ids');
    }
}
