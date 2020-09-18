| About wholesale

WooCommerce Wholesale is a WooCommerce e-store extension that allows to create custom product tables, which make buying far more quicker and convenient. Although it can be useful for retail buyers, Wholesale is directed to wholesale buyers, who buy many different variations. Wholesale allows them to complete their order on one page through clear tables, increasing their productivity significantly.



| How does it work?

Completing orders with Wholesale is really easy. A user specifies a desired number of products and then clicks "Add to cart". It is really that simple.



| Installation

1. Unzip the files and load the folder into your plugins folder (wp-content/plugins/)
2. Now activate the plugin in your WordPress admin area.



| How do I create a table?

Well all you have to do to add a table to a page is to type something like this:

[wholesale columns="buy,sale-price/,tally/,total/" products="40|130" buy="vertical-attribute/Color Combo|Mod,label/M|W,horizontal-attribute/Size"]



| How do I create such a shortcode?

A shortcode consists of 4 optional tags: columns, products, buy and button_for_every_table.

    columns - Put there names of columns, you want to display. You can rearrange them as you wish. Available columns are:

        SKU - displays product SKU
        image - displays product image
        rating - displays product rating
        regular-price - displays product regular price
        sale-price - displays product sale price. If a product is not on sale, then a regular price is displayed. Use it to display a final price.
        buy - In case of one simple product, an input is displayed, allowing a user to specify how many products is ordered. If there are more simple products or variations, it displays a label for each product and/or attributes and inputs
        tally - displays how many products from a row a user have added.
        total - displays total sum a user will pay
        stock - displays how many items are in stock

    If you do not specify columns, default columns are displayed: image, buy, sale-price, tally and total.
    By default, a column heading are the same as a column name, you can easily change that by adding '/' at the end of a column name, just like 'sale-price/Price'. If you do not want a heading, just add  '/', like 'sale-price/' - This creates a column with an empty heading. This applies to all columns except 'buby', because it prints attributes.


    products - Put products IDs there. If you want to connect two or more products under one title, use '|' symbol to connect them. For instance, 'products="1|2|3|4"' creates a connection of products with IDs 1, 2, 3 and 4. You can add a name to every connection, as in case of a column, by adding '/your-name' at the end of a connection. Especially '/' left at the end of a connection creates an empty title. Remember that you can have as many connections as you want, use commas to separate them, like 'products="1|2/First title,3|4/Second title,5|6/'. Remember to add every item in connection a label.

    categories - Put categories slugs there. You can add mutliple categories, like 'categories="category1,category2"'.

    tags - Put products tags there. You can add multiple categories, like 'tags="tag1,tag2"'. You can freely use this option with categories.

    buy - add this tag if you:

            - have connected products in a 'products' tag
            - have added variable products

    In this tag, you can specify attributes and labels which are used to generate a table, in detail:

        vertical-attribute - add '/' after this and write atrributes you want to add, connected by '|', like 'buy="vertical-attribute/Color|Type|Size"'
        horizontal-attribute - add '/' after this and write atrribute you want to add e.g. 'buy="horizontal-attribute/Size"'. There can be only one attribute name!
        label - add '/' after this and write labels you want to add to each product in connection connected by '|', like 'label/Man|Woman|Child"'

    Settings:

    button_for_every_table - add this tag with value 'yes' to last wholesale shortcode if you want every table to have it's own button - button_for_every_table="yes". By default it's value is 'no'.

    grand_total - add this tag with value 'yes' to last wholesale shortcode if you want every table to have grand total at the bottom

    link_in_title - add this tag with value 'yes' to last wholesale shortcode if you want every table to have a link to a product in the title

    one_product_one_image - add this tag with value 'yes' to last wholesale shortcode if you want every table to have one image for every product variation

| General recipe for shortcode:

[!wholesale columns="SKU/Title,image/Title,rating/Title,buy/Title,regular-price/Title,sale-price/Title,tally/Title,total/Title" products="ID1|ID2/Title,ID3|ID4/Title,ID5|ID6/Title" buy="vertical-attribute/Attribute1|Attribute2,label/label1|label2,horizontal-attribute/Attribute3"]
Remove '!' at the beginning, remove unwanted columns, change titles, attributes and labels and it is ready!


FOR EXAMPLES AND MORE INSTRUCTIONS PLEASE VISIT http://wholesale-demo.optart.biz/
