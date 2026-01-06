@php
    
    
    



    $productFilters = \App\Models\ProductsFilter::productFilters(); 
    
@endphp


<script> 
    
    $(document).ready(function() {

        



        
        $('#sort').on('change', function() { 
            var sort = $('#sort').val(); 
            var url  = $('#url').val(); 


            


            var size  = get_filter('size'); 
            var color = get_filter('color'); 
            var price = get_filter('price'); 
            var brand = get_filter('brand'); 


            
            
            @foreach ($productFilters as $filters) 
                var {{ $filters['filter_column'] }} = get_filter('{{ $filters['filter_column'] }}'); 
            @endforeach


            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                url    : url, 
                type   : 'Post',
                data   : { 

                    
                    @foreach ($productFilters as $filters) 
                        {{ $filters['filter_column'] }}: {{ $filters['filter_column'] }}, 
                    @endforeach
                    sort: sort, url: url, size: size, color: color, price: price, brand: brand

                },
                success: function(data) {
                    $('.filter_products').html(data);
                },
                error  : function() {
                    alert('Lỗi');
                }
            });
        });

        
        

        
        
        
        @foreach ($productFilters as $filter) 

            
            $('.{{ $filter['filter_column'] }}').on('click', function() { 
                var url  = $('#url').val(); 
                var sort = $('#sort option:selected').val(); 


                var size  = get_filter('size'); 
                var color = get_filter('color'); 
                var price = get_filter('price'); 
                var brand = get_filter('brand'); 


                
                
                @foreach ($productFilters as $filters) 
                    var {{ $filters['filter_column'] }} = get_filter('{{ $filters['filter_column'] }}'); 
                @endforeach



                $.ajax({
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                    url    : url, 
                    method : 'Post',
                    data   : {

                        
                        @foreach ($productFilters as $filters) 
                            {{ $filters['filter_column'] }}: {{ $filters['filter_column'] }}, 
                        @endforeach
                        url: url, sort: sort, size: size, color: color, price: price, brand: brand

                    },
                    success: function(data) {
                        $('.filter_products').html(data); 
                    },
                    error  : function() {
                        alert('Lỗi');
                    }
                });
            });

        @endforeach



        
        
        
        
        $('.size').on('click', function() { 
            var url  = $('#url').val(); 
            var sort = $('#sort option:selected').val(); 


            var size  = get_filter('size'); 
            var color = get_filter('color'); 
            var price = get_filter('price'); 
            var brand = get_filter('brand'); 


            
            
            @foreach ($productFilters as $filters) 
                var {{ $filters['filter_column'] }} = get_filter('{{ $filters['filter_column'] }}'); 
            @endforeach



            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                url    : url, 
                method : 'Post',
                data   : {

                    
                    @foreach ($productFilters as $filters) 
                        {{ $filters['filter_column'] }}: {{ $filters['filter_column'] }}, 
                    @endforeach
                    url: url, sort: sort, size: size, color: color, price: price, brand: brand

                },
                success: function(data) {
                    $('.filter_products').html(data); 
                },
                error  : function() {
                    alert('Lỗi');
                }
            });
        });

        
        
        
        
        $('.color').on('click', function() { 
            var url  = $('#url').val(); 
            var sort = $('#sort option:selected').val(); 

            var size  = get_filter('size'); 
            var color = get_filter('color'); 
            var price = get_filter('price'); 
            var brand = get_filter('brand'); 


            
            
            @foreach ($productFilters as $filters) 
                var {{ $filters['filter_column'] }} = get_filter('{{ $filters['filter_column'] }}'); 
            @endforeach



            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                url    : url, 
                method : 'Post',
                data   : {

                    
                    @foreach ($productFilters as $filters) 
                        {{ $filters['filter_column'] }}: {{ $filters['filter_column'] }}, 
                    @endforeach
                    url: url, sort: sort, size: size, color: color, price: price, brand: brand

                },
                success: function(data) {
                    $('.filter_products').html(data); 
                },
                error  : function() {
                    alert('Lỗi');
                }
            });
        });

        
        
        
        
        $('.price').on('click', function() { 
            var url  = $('#url').val(); 
            var sort = $('#sort option:selected').val(); 

            var size  = get_filter('size'); 
            var color = get_filter('color'); 
            var price = get_filter('price'); 
            var brand = get_filter('brand'); 


            
            @foreach ($productFilters as $filters) 
                var {{ $filters['filter_column'] }} = get_filter('{{ $filters['filter_column'] }}'); 
            @endforeach



            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                url    : url, 
                method : 'Post',
                data   : {

                    
                    @foreach ($productFilters as $filters) 
                        {{ $filters['filter_column'] }}: {{ $filters['filter_column'] }}, 
                    @endforeach
                    url: url, sort: sort, size: size, color: color, price: price, brand: brand

                },
                success: function(data) {
                    $('.filter_products').html(data); 
                },
                error  : function() {
                    alert('Lỗi');
                }
            });
        });

        
        
        
        
        $('.brand').on('click', function() { 
            var url  = $('#url').val(); 
            var sort = $('#sort option:selected').val(); 

            var size  = get_filter('size'); 
            var color = get_filter('color'); 
            var price = get_filter('price'); 
            var brand = get_filter('brand'); 


            
            @foreach ($productFilters as $filters) 
                var {{ $filters['filter_column'] }} = get_filter('{{ $filters['filter_column'] }}'); 
            @endforeach



            $.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                url    : url, 
                method : 'Post',
                data   : {

                    
                    @foreach ($productFilters as $filters) 
                        {{ $filters['filter_column'] }}: {{ $filters['filter_column'] }}, 
                    @endforeach
                    url: url, sort: sort, size: size, color: color, price: price, brand: brand

                },
                success: function(data) {
                    $('.filter_products').html(data); 
                },
                error  : function() {
                    alert('Lỗi');
                }
            });
        });
    });
</script>