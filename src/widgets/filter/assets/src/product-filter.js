/*!
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.11.2017
 */
(function(sx, $, _)
{
    sx.classes.ProductFilters = sx.classes.Component.extend({

        _init: function()
        {},

        _onDomReady: function()
        {
            var jHiddenWrapper = $('.sx-hidden-filters');
            $("input", jHiddenWrapper).on('change', function () {
                $(this).closest('form').submit();

            });
        },

        _onWindowReady: function()
        {}
    });
})(sx, sx.$, sx._);