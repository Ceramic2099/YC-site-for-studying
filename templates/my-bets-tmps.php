<section class="rates container">
    <h2>Мои ставки</h2>
    <?php if (!empty($bets)): ?>
    <table class="rates__list">
        <?php foreach ($bets as $bet): ?>
            <?php $time = get_dt_range($bet['date_finish']); ?>
        <tr class="rates__item <?php if ($bet['winner_id'] == $bet['user_id']):?> rates__item--win <?php elseif ($time[0] == 0): ?> rates__item--end <?php endif;?>">
            <td class="rates__info">
                <div class="rates__img">
                    <img src="<?= $bet['img']; ?>" width="54" height="40" alt="<?= $bet['name_category']; ?>">
                </div>
                <div>
                <h3 class="rates__title"><a href="/lot.php?id=<?= $bet['id']; ?>"><?= $bet['title']; ?></a></h3>
                <p><?php if ($bet['winner_id'] == $bet['user_id']):?><p><?= $bet['contacts']; ?></p><?php endif; ?></p>
                </div>
            </td>
            <td class="rates__category">
                <?= $bet['name_category']; ?>
            </td>
            <td class="rates__timer">
                <div class="timer <?php if ($time[0] < 1 && $time[0] != 0): ?>timer--finishing <?php elseif ($time[0] == 0 && $bet['winner_id'] == $bet['user_id']): ?>timer--win<?php elseif ($time[0] == 0): ?>timer--end<?php endif; ?>">
                    <?php if ($time[0] != 0): ?>
                    <?= "$time[0] : $time[1]"; ?>
                    <?php elseif ($bet['winner_id'] == $bet['user_id']): ?>
                    Ставка выиграла
                    <?php else: ?>
                    Торги окончены
                    <?php endif; ?>
                </div>
            </td>
            <td class="rates__price">
                <?= price($bet['price_bet']); ?>
            </td>
            <td class="rates__time">
                <?= $bet['date_bet']; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</section>