<?php

namespace CachetHQ\Cachet\Listeners;

use CachetHQ\Cachet\Bus\Commands\Incident\CreateIncidentCommand;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed;

class WhenMonitorsFail
{
	public function handle( UptimeCheckFailed $event )
	{
		$component = Component::whereLink( $event->monitor->url )->first();

		if ( $component )
		{
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
