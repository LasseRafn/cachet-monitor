<?php

namespace CachetHQ\Cachet\Listeners;

use CachetHQ\Cachet\Bus\Commands\Incident\CreateIncidentCommand;
use CachetHQ\Cachet\Bus\Commands\Incident\UpdateIncidentCommand;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Spatie\UptimeMonitor\Events\UptimeCheckFailed;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;
use Spatie\UptimeMonitor\Events\UptimeCheckSucceeded;

class WhenMonitorsSucceed
{
	public function handle( UptimeCheckSucceeded $event )
	{
		$component = Component::whereLink( $event->monitor->url )->first();

		if ( $component && $component->status !== 1 )
		{
			$component->status = 1;
			$component->save();
		}
	}
}
