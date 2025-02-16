define(['dojo/_base/declare'], (declare) => {
    return declare('pyramido.market', null, {
        /**
         * Dependencies:
         * dojo
         * stocks
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
            const first = +this.get_missing_index();

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
            if (this.callback_object) {
                this.callback_object[this.callback_method](this.stocks[selected_element_id]);
            }
        },
        unsubscribe() {
            this.get_stock_keys(this.category_subscription).forEach(key => this.make_stock_unselectable(key));
            delete this.callback_object;
            delete this.callback_method;
            delete this.category_subscription;
        },
        get_missing_index() {
            return Object.keys(this.stocks).find(key => 0 == this.stocks[key].count()).slice(7);
        },
        move(from, to) {
            this.stocks[from].removeFromStockById(1);
            this.stocks[to].addToStockWithId(this.dominoes[from].id, 1);

            this.dominoes[to] = this.dominoes[from];
        },
        delete(stock_id) {
            this.stocks[stock_id].removeFromStockById(1);
            delete this.dominoes[stock_id];

        },
        /**
         * setup
         */
        setup(gamedatas) {
            this.setup_market_structure();
            this.setup_market_quarry(gamedatas.quarry);
            this.setup_market_next(gamedatas.next);
            Object.values(this.stocks).forEach(stock => {
                this.dojo.connect(stock, 'onChangeSelection', this, 'domino_selected');
            });
        },
        /**
         * Private methods
         */
        get_stock_keys(prefix) {
            return Object.keys(this.stocks).filter(key => key.startsWith(prefix));
        },
        make_stock_selectable(key) {
            this.dojo.addClass(key, 'selectable')
        },
        make_stock_unselectable(key) {
            this.dojo.removeClass(key, 'selectable')
        },
        setup_market_structure() {
            this.document.getElementById('game_play_area').insertAdjacentHTML('beforeend', `
                <div id="market">
                    <table style="justify-content:center;">
                        <tr>
                            <table>
                                <tr><td>.......................................</td><td>.......................................</td><td>.......................................</td><td>.......................................</td></tr>
                                <tr id="next"></tr>
                            </table>
                        </tr>
                        <tr style="background-color:powderblue;">
                            <table>
                                <tr><td>.....................</td><td>..........................................</td><td>.......................................</td><td>.............................................</td></tr>
                                <tr id="quarry"><td class=".tile"><div style="display: inline-block" ></div></td></tr>
                            </table>
                        </tr>
                    </table>
                </div>
            `);
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
                this.fill(category_name + '-' + element.index, element);
            });
        },
        fill(stock_id, domino_specification) {
            this.dominoes[stock_id] = this.domino_factory.create_domino_from(domino_specification);
            this.stocks[stock_id].addToStockWithId(this.dominoes[stock_id].id, 1);

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
            this.stocks[element_id] = hand;
        },
        get_card_type_id(row, i) {
            return row * 90/this.image_items_per_row + i;
        },
    });
});
