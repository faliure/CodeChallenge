<?php
/**
 * @var array  $scores
 * @var string $title
 */
?>
<h4>{!! $title ?? 'Leaderboard' !!}</h4>
<ul style="padding: 0px;">
    @foreach ($scores as $rank => $rankItem)
        <li class="courseRanking__rankItem"
            style="display: flex; flex-direction: row; padding: 10px;">
            <div class="position"
                style="font-size: 28px; color: rgb(132, 132, 132); text-align: right; width: 80px; padding-right: 10px;">
                {{ $rank + 1 }}
            </div>
            <div class="info">
                <div style="font-size: 16px;">
                    {!! $rankItem->user_id === auth()->user()->id ? '<b>' : '' !!}
                        {{ $rankItem->user_name }}
                    {!! $rankItem->user_id === auth()->user()->id ? '</b>' : '' !!}
                </div>
                <div class="score" style="font-size: 10px; color: rgb(132, 132, 132);">
                    {{ $rankItem->score }} PTS (+93 -- TODO --)
                </div>
            </div>
        </li>
        {!! !$loop->last && !isset($scores[$rank + 1]) ? '<hr />' : '' !!}
    @endforeach
</ul>