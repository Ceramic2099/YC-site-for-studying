<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">
        <!--заполните этот список из массива категорий-->
        <?php foreach ($categories as $key => $value): ?>
            <li class="promo__item promo__item--<?=$value['character_code'];?>">
                <a class="promo__link" href="/search.php?search=<?=$value['name_category'];?>"><?=$value['name_category'];?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($lots as $value): ?>
            <!--заполните этот список из массива с товарами-->
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=htmlspecialchars($value['img']);?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=htmlspecialchars($value['name_category']);?></span>
                    <h3 class="lot__title"><a class="text-link" href="/lot.php?id=<?=htmlspecialchars($value['id']);?>"><?=htmlspecialchars($value['title']);?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=htmlspecialchars(price($value['start_price']));?></span>
                        </div>
                        <?php $timer = get_dt_range(htmlspecialchars($value['date_finish'])); ?>
                            <div class="lot__timer timer <?php if ($timer[0] < 1) : echo "timer--finishing"; endif; ?>">
                            <?= "$timer[0] : $timer[1]";?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>

