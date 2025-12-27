define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.market', null, {
        /**
         * Dependencies:
         * dojo
         * stock_class
         * document
         */
        /**
         * Use case creation:
         * Permanently subscribes to each stock but ignores selections until client subscribes
         */
        DOMINOES_PER_ROW: 10,
        PIXELS_PER_TILE: 80,

        constructor(dependencies) {
            this.clone(dependencies);
            this.cardwidth = 2 * this.PIXELS_PER_TILE;
            this.cardheight = this.PIXELS_PER_TILE;
            this.image_items_per_row = this.DOMINOES_PER_ROW;

            this.stocks = {};
            this.dominoes = {};
            this.selectables = {};
        },
        clone(properties){
            for (var property in properties) {
                this[property] = properties[property];
            }
        },
        /**
         * Support single subscription
         */
        subscribe_to_next(callback_object, callback_method) {
            this.subscribe(callback_object, callback_method, 'next');
            const first = +this.get_missing_index().slice(7);

            this.make_stock_selectable('next-' + first);
            this.make_stock_selectable('next-' + (first + 1));
        },
        subscribe_to_quarry(callback_object, callback_method) {
            this.subscribe(callback_object, callback_method, 'quarry');
            this.get_stock_keys('quarry').forEach(key => this.make_stock_selectable(key));
        },
        subscribe(callback_object, callback_method, category) {
            this.callback_object = callback_object;
            this.callback_method = callback_method;
            this.category_subscription = category;
        },
        /**
         * Notify subscriber when domino selected
         */
        domino_selected(selected_element_id) {
            if (selected_element_id in this.selectables) {
                this.callback_object[this.callback_method](this.dominoes[selected_element_id]);
            }
        },
        unsubscribe() {
            this.get_stock_keys(this.category_subscription).forEach(key => this.make_stock_unselectable(key));
            delete this.callback_object;
            delete this.callback_method;
            delete this.category_subscription;
        },
        get_missing_index() {
            return Object.keys(this.stocks).find(key => 0 == this.stocks[key].count());
        },
        move(from, to) {
            this.stocks[from].removeFromStockById(1);
            this.stocks[to].addToStockWithId(this.dominoes[from].id, 1);

            this.dominoes[to] = this.dominoes[from];
            this.dominoes[to].element_id = to;
        },
        delete(element_id) {
            this.stocks[element_id].removeFromStockById(1);
            delete this.dominoes[element_id];

        },
        /**
         * setup
         */
        setup(dependencies, gamedatas) {
            this.clone(dependencies);

            this.setup_market_structure();
            this.setup_market_quarry(gamedatas.quarry);
            this.setup_market_next(gamedatas.next);
        },
        /**
         * Private methods
         */
        get_stock_keys(prefix) {
            return Object.keys(this.stocks).filter(key => key.startsWith(prefix));
        },
        make_stock_selectable(key) {
            this.dojo.addClass(key, 'selectable');
            this.selectables[key] = key;
        },
        make_stock_unselectable(key) {
            this.dojo.removeClass(key, 'selectable');
            delete this.selectables[key];
        },
        setup_market_structure() {
            this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                <div style="justify-content:center;">
                    <table style="justify-content:center;">
                        <tr>
                            <table>
                                <tr><td>........................................</td><td>..........................Quarry..</td><td>replenishments..............</td><td>........................................</td></tr>
                                <tr id="next"></tr>
                            </table>
                        </tr>
                        <tr style="background-color:powderblue;">
                            <table>
                                <tr><td>....................</td><td>........................................</td><td>..............Quarry..............</td><td>..........................................................</td></tr>
                                <tr id="quarry"><td class=".tile"><div style="display: inline-block" ></div></td></tr>
                            </table>
                        </tr>
                        <tr style="background-color:powderblue;">
                            <table>
                                <tr><td>........................................</td><td>........................................</td><td>......................................</td><td>........................................</td></tr>
                                <tr id="next"></tr>
                            </table>
                        </tr>
                    </table>
                </div>
            `);
            this.game.addTooltip('next', _('Dominoes to replenish quarry'), '');
            this.game.addTooltip('quarry', _('Building blocks available for pyramids'), '');

        },
        setup_market_next(next) {
            for (const x of Array(4).keys()) {
                this.setup_market_element('next', x + 1);
            }
            this.fill_market_category(next, 'next');
        },
        setup_market_quarry(quarry) {
            for (const x of Array(3).keys()) {
                this.setup_market_element('quarry', x + 1);
            }
            this.fill_market_category(quarry, 'quarry');
        },
        fill_market_category(elements, category_name) {
            console.log(elements);
            Object.values(elements).forEach(element => {
                this.fill(element.element_id, element);
            });
        },
        fill(element_id, domino_specification) {
            this.dominoes[element_id] = this.domino_factory.create_domino_from(domino_specification);
            this.stocks[element_id].addToStockWithId(this.dominoes[element_id].id, 1);

        },
        setup_market_element(category, index) {
            let element_id = category + '-'+ index;
            this.document.getElementById(category).insertAdjacentHTML('beforeend', `
                <td class=".single_card">
                <div id = "${category}-${index}" style="display: inline-block" ></div>
                </td>
            `);
            hand = new this.stock_class();
            hand.create(this.game, this.game.get_element(element_id), this.cardwidth, this.cardheight);
            hand.image_items_per_row = this.image_items_per_row;
            for (let row = 0; row < 90/hand.image_items_per_row; row++) {
                for (let i = 0; i < hand.image_items_per_row; i++) {
                    let card_type_id = this.get_card_type_id(row, i);
                    hand.addItemType(card_type_id, card_type_id, this.gamethemeurl+'img/' + 'dominoesx' + this.PIXELS_PER_TILE + '.png', card_type_id);
                }
            }
            this.dojo.connect(hand, 'onChangeSelection', this, 'domino_selected');
            this.stocks[element_id] = hand;
        },
        get_card_type_id(row, i) {
            return row * this.image_items_per_row + i;
        },
    });
});
