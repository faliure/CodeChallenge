<?php
/**
 * @var App\Rank  $rank
 * @var bool      $isGlobal  This is either a global or a country rank
 */
?>
<div class="col-md-6">
    <h4>
        You are ranked <b> {{ $rank->ordinalUserPosition() ?? '--' }} </b>
        {{ $rank->isGlobal() ? 'Worldwide' : 'in ' . auth()->user()->country->name }}
    </h4>

    <ul style="padding: 0px;">
        @php
            $currentUserId = auth()->id();
            $userScore = $rank->userRankItem()->score;
        @endphp

        @foreach ($rank->items() as $rankItem)
            <li class="courseRanking__rankItem"
                style="display: flex; flex-direction: row; padding: 10px;">
                <div class="position"
                    style="font-size: 28px; color: rgb(132, 132, 132); text-align: right; width: 80px; padding-right: 10px;">
                    {{ $rankItem->position }}
                </div>
                <div class="info">
                    <div style="font-size: 16px;">
                        {!! $rankItem->user_id === $currentUserId ? '<b style="color:#000">' : '' !!}
                            {{ $rankItem->user_name }}
                        {!! $rankItem->user_id === $currentUserId ? '</b>' : '' !!}
                    </div>
                    <div class="score" style="font-size: 10px; color: rgb(132, 132, 132);">
                        {{ $rankItem->score }} PTS
                        {{ $rankItem->score > $userScore ? '(+' . ($rankItem->score - $userScore) . ')' : ''  }}
                    </div>
                </div>
            </li>

            {!! !$loop->last && !$rank->hasNext($rankItem) ? '<hr />' : '' !!}
        @endforeach
    </ul>
</div>