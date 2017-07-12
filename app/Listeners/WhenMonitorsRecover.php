<?php

namespace CachetHQ\Cachet\Listeners;

use CachetHQ\Cachet\Bus\Commands\Incident\CreateIncidentCommand;
use CachetHQ\Cachet\Bus\Commands\Incident\UpdateIncidentCommand;
use CachetHQ\Cachet\Bus\Commands\IncidentUpdate\CreateIncidentUpdateCommand;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;

class WhenMonitorsRecover
{
	public function handle( UptimeCheckRecovered $event )
	{
		$component = Component::whereLink( $event->monitor->url )->first();

		if ( $component )
		{
			Auth::login( User::first() );

			if ( $incident = $component->incidents()->whereName( 'Uptime Monitor' )->whereStatus( 2 )->latest()->first() )
			{
				Auth::login( User::first() );

				dispatch( new CreateIncidentUpdateCommand(
					$incident,
					4,
					'Recovered',
					User::first()
				) );
			}

			$component->status = 1;
			$component->save();
		}
	}
}
