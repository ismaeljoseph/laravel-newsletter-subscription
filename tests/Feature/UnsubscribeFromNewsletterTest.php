<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Riverskies\LaravelNewsletterSubscription\Mail\NewsletterSubscriptionConfirmation;
use Riverskies\LaravelNewsletterSubscription\NewsletterSubscription;
use Tests\TestCase;

class UnsubscribeFromNewsletterTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function people_can_unsubscribe_from_newsletters()
    {
        $subscription = factory(NewsletterSubscription::class)->create();

        $response = $this->get("/unsubscribe/{$subscription->hash}");

        $response->assertRedirect('/');
        $response->assertSessionHas('flash', 'You will no longer receive our newsletter at ' . $subscription->email);
        $this->assertDatabaseMissing($this->table, ['email'=>$subscription->email]);
    }

    /** @test */
    public function the_newsletter_confirmation_email_has_the_correct_unsubscribe_link()
    {
        $subscription = factory(NewsletterSubscription::class)->create();
        $message = new NewsletterSubscriptionConfirmation($subscription);

        $this->assertContains(url("/unsubscribe/{$subscription->hash}"), $message->build()->render());
    }
}