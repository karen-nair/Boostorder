(function ($) {

    var client = {

        fireAjax: function (arguments, callback) {

            $.ajax(
                {
                    url: frontend.ajax_url,
                    'data': arguments,
                    error: function (msg, b, c) {

                        console.debug('error');
                        console.debug(msg);
                        console.debug(b);
                        console.debug(c);
                    },
                    dataType: 'json',
                    cache: false,
                    success: function (result) {
                        callback(result);
                        return false;
                    },
                    type: 'POST'
                }
            );

        },

       

    };

//events
// localStorage.clear(); 

//on page load update cart counter
updateCartCounter();
function updateCartCounter()
{
    $('#cartBody').html('');
    var cart = JSON.parse(localStorage.getItem("incart"));
    if (cart != null) {
        var totalCartItem = cart.length;

            var sum = 0;
            $.each(cart, function(key, value) {
                sum += Number(value['price']);
                $('#cartBody').append('<tr id="'+value['id']+'">'
                        +'<td data-item-image><img class="cart-image" src="'+value['image']+'"></td>'
                        +'<td data-item-name>'+value['name']+'</td>'
                        +'<td data-item-qty>'+value['qty']+'</td>'
                         +'<td data-item-price>RM <span data-item-price-value>'+value['price']+'</span></td>'
                    
                    +'</tr>');
            });
        $('[data-total]').html(sum);
        
        if (cart != null) {
            $('[data-cart-total]').text(totalCartItem);
        }
    }
    
    

}

var incart = {};
var currentItem = null;

var modify;

//add to cart
$('.addtocart').on('click', function() {

    currentItem = $(this).val();
    var itemImage = $(this).data('image');
    var itemName = $(this).data('name');
    var itemPrice = $(this).data('price');


    incart = JSON.parse(localStorage.getItem("incart"));

    if (incart == null) {
        console.log('null');
        incart = [{
        'id': currentItem,
        'image': itemImage,
        'name': itemName,
        'price': itemPrice,
        'qty':1
        }];

        localStorage.setItem("incart", JSON.stringify(incart));
        updateCartCounter();

    } else {

        //check if current item exist in the cart
       var item = $.grep(incart, function(item) {
            return item.id == currentItem;
        });

        if (item.length) {
            console.log('item exist in cart');
            $('#modifyCart .message').text('This item already exist in the cart. Do you want to add more?');
            $('#addMore').val(currentItem);
            $('#removeFromCart').val(currentItem);

            $('.modify-cart').trigger('click');
        } else {
            
             incart.push({
            'id': currentItem,
            'image': itemImage,
            'name': itemName,
            'price': itemPrice,
            'qty':1
            });
                  
            localStorage.setItem("incart", JSON.stringify(incart));
            updateCartCounter();
            alert('Item added into cart');
        }
    }

});

$('#addMore').on('click', function() {

    var currentItem = $(this).val();
    
     $.each(incart, function(key, value) {
        if (value['id'] == currentItem) {
         
            value['qty'] = value['qty'] + 1;
            value['price'] = parseInt(value['qty'] * value['price']);
        }
     });

    localStorage.setItem("incart", JSON.stringify(incart));
    updateCartCounter();

    $('#modifyCart .message').text('Item added to the cart');
    setTimeout(function(){ 
     $('#modifyCart .close-button').trigger('click');
    }, 3000);

    

});


$('#removeFromCart').on('click', function() {

    var currentItem = $(this).val();
    var incart = JSON.parse(localStorage.getItem("incart"));

    var item = $.grep(incart, function(item) {
            return item.id == currentItem;
        });

        if (item.length) {
             incart.splice(incart.indexOf(currentItem));
             localStorage.setItem("incart", JSON.stringify(incart));
             updateCartCounter();
        }


    $('#modifyCart .message').text('Item removed from the cart');

    setTimeout(function(){ 
     $('#modifyCart .close-button').trigger('click');
    }, 3000);


});

$('#submitOrder').on('click', function() {

    var total = $('#cart tfoot').find('[data-total]').text();
     var ary = [{Total: total}];
        $(function () {
            $('#cart tbody tr').each(function (a, b) {

                var id = $(b).attr('id');
                var image = $(b).find('[data-item-image] img').attr('src');

                var name = $(b).find('[data-item-name]').text();
                var qty = $(b).find('[data-item-qty]').text();
                var price = 'RM '+$(b).find('[data-item-price]').text();

                ary.push({ ProductID: id, Image: image, Name: name, Qty: qty, Price: price });
               
            });


             client.fireAjax({
                    'action':'submitOrder',
                    'data':{
                        'order': ary

                    }
                }, function (result) {
                    localStorage.clear(); 
                    updateCartCounter();
                   $('.close-button').trigger('click');
                });
        });
});

//clear cart
$('[data-clear-cart]').on('click', function() {

    var txt;
    var r = confirm("Are you sure want to empty the cart?");
    if (r == true) {
        localStorage.clear(); 
        $('[data-total]').html('');
        $('[data-cart-total]').html('');
        updateCartCounter();
    } 
});


//update order status 
$('[data-order-status]').on('change', function() {
  
    var td = $(this).closest('td');
    var orderID = $(td).find('select').attr('id');
    var orderStatus = $(this).val();

      client.fireAjax({
                    'action':'updateOrderStatus',
                    'data':{
                        'orderID': orderID,
                        'status': orderStatus

                    }
                }, function (result) {
                    alert('Order status changed');
                    if (orderStatus == 0) {
                        $(td).removeClass('approved').addClass('pending');
                    } else {
                        $(td).removeClass('pending').addClass('approved');
                    }
                   
                });
});


//check for approved order  on interval
var dom = [];
$("[data-approved-order] a span").each(function(){
  dom.push($(this).text());
});
console.log(dom.length);
setInterval(function(){ 

          client.fireAjax({
                    'action':'monitorOrderStatus'
                }, function (result) {
                   var order = result.orderID;
                    
                    $(order).each(function(x, y) {
                      if(jQuery.inArray(y.orderID, dom) != -1) {
                        } else {
                            $('[data-approved-order-value]').text(parseInt($('[data-approved-order-value]').text())+ 1);
                            
                            if (dom.length > 0) {

                                $('<a href="http://localhost/boostorder/order/'+y.orderID+'">#<span>'+y.orderID+'</span></a>')
                                .insertAfter("[data-approved-order] a:last");
                             } else {
                                if ($('[data-approved-order] a').length <= 0) {
                                    $('<a href="http://localhost/boostorder/order/'+y.orderID+'">#<span>'+y.orderID+'</span></a>')
                                    .insertAfter("[data-approved-order] span");
                                }
                                
                             }

                            // if (dom.length > 0) {

                             dom.push(y.orderID);
                             console.log(dom);

                            // } else {
                            //     dom[y.orderID];
                                // console.log(dom);
                            // }
                        } 

                    });
                        
             

                    
                   
                });

}, 3000);
//initiazlize foundation
$(document).foundation();
})(jQuery);




