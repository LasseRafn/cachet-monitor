<?php

namespace CachetHQ\Cachet\Listeners;

use CachetHQ\Cachet\Bus\Commands\Incident\CreateIncidentCommand;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\Incident;
use CachetHQ\Cachet\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed;

class WhenMonitorsFail
{
	public function handle( UptimeCheckFailed $event ) {
		/** @var Component $component */
		$component = Component::whereLink( $event->monitor->url )->first();

		if ( $component ) {
			/** @var Incident $latestIncident */
			if ( (int) $component->status === 2 && $latestIncident = $component->incidents()->whereNull('deleted_at')->whereName( 'Uptime Monitor' )->whereStatus( 2 )->latest()->first() ) {
				if ( $latestIncident->message === ( $event->monitor->uptime_check_failure_reason ?? '' ) ) {
					return;
				}
			}

			Auth::login( User::first() );

			dispatch( new CreateIncidentCommand(
				'Uptime Monitor',
				2,
				$event->monitor->uptime_check_failure_reason ?? '',
				true,
				$component->id,
				4,
				true,
				false,
				null,
				null
			) );
		}
	}
}
