<section class="lot-item container">
    <h2><?= $lot['title']; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= $lot['img']; ?>" width="730" height="548" alt="<?= $lot['title']; ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?= $lot['name_category']; ?></span></p>
            <p class="lot-item__description"><?= $lot['lot_description']; ?></p>
        </div>
        <div class="lot-item__right">
            <?php if(!empty($_SESSION)): ?>
            <div class="lot-item__state">
                <?php $timer=get_dt_range(htmlspecialchars($lot['date_finish'])); ?>
                <div class="lot-item__timer timer <?php if($timer[0] < 1) : echo "timer--finishing"; endif; ?>">
                    <?= "$timer[0] : $timer[1]";?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?php if (isset($bets[0]['price_bet'])): echo price($bets[0]['price_bet']); else: echo price($lot['start_price']); endif;?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?=htmlspecialchars(price($lot['step']));?></span>
                    </div>
                </div>
                <form class="lot-item__form" action="/lot.php" method="post" autocomplete="off">
                    <p class="lot-item__form-item form__item <?= isset($errors['cost']) ? "form__item--invalid" : "" ; ?>">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="text" name="cost" placeholder="<?=htmlspecialchars(price($lot['step']));?>">
                        <input type="hidden" name="lot_id" value="<?=htmlspecialchars($lot['id']); ?>">
                        <span class="form__error"><?= $errors['cost'] ?? ""; ?></span>
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
            </div>
            <?php endif; ?>
            <div class="history">
                <h3>История ставок (<span>10</span>)</h3>
                <table class="history__list">
                    <?php if (!empty($bets)): ?>
                    <?php foreach ($bets as $bet): ?>
                    <tr class="history__item">
                        <td class="history__name"><?= $bet['user_name']; ?></td>
                        <td class="history__price"><?= price($bet['price_bet']); ?></td>
                        <td class="history__time"><?= $bet['date_bet']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</section>
