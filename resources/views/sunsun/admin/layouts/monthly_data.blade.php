@if(isset($data['monthly_data'][$day['full_date']]))
    @if($type == 'male')
        @if(!isset($data['monthly_data'][$day['full_date']][$type][$time]) || count($data['monthly_data'][$day['full_date']][$type][$time]) == 0)
        ●
        @elseif((count($data['monthly_data'][$day['full_date']][$type][$time]) == 12) && ($time == 0))
        ×
        @elseif((count($data['monthly_data'][$day['full_date']][$type][$time]) == 18) && ($time == 1))
        ×
        @elseif((count($data['monthly_data'][$day['full_date']][$type][$time]) == 3) && ($time == 2))
        ×
        @else
        ▲
        @endif
    @elseif($type == 'female')
        @if(!isset($data['monthly_data'][$day['full_date']][$type][$time]) || count($data['monthly_data'][$day['full_date']][$type][$time]) == 0)
        ●
        @elseif((count($data['monthly_data'][$day['full_date']][$type][$time]) == 14) && ($time == 0))
        ×
        @elseif((count($data['monthly_data'][$day['full_date']][$type][$time]) == 19) && ($time == 1))
        ×
        @elseif((count($data['monthly_data'][$day['full_date']][$type][$time]) == 3) && ($time == 2))
        ×
        @else
        ▲
        @endif
    @elseif($type == 'pet')
        @if(!isset($data['monthly_data'][$day['full_date']][$type][$time]) || count($data['monthly_data'][$day['full_date']][$type][$time]) == 0)
        ●
        @elseif(count($data['monthly_data'][$day['full_date']][$type][$time]) == 2)
        ×
        @else
        ▲
        @endif
    @elseif($type == 'wt')
        @if(!isset($data['monthly_data'][$day['full_date']][$type][$time]) || count($data['monthly_data'][$day['full_date']][$type][$time]) == 0)
        ●
        @elseif((count($data['monthly_data'][$day['full_date']][$type][$time]) == 6) && ($time == 0))
        ×
        @elseif((count($data['monthly_data'][$day['full_date']][$type][$time]) == 5) && ($time == 1))
        ×
        @else
        ▲
        @endif
    @endif
@else
●
@endif
