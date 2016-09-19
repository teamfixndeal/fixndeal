
<header class="1" id="header">


            <div id="top_line">
            <div class="container">
                <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">
        <p class="p_top_header">  
           You Are In  <i class="fa fa-phone"></i> {$LinksDetails.telephone}   	     </p>
    </div>
    <div class="col-md-6 col-sm-6 col-xs-6">
    
        	<div class="select-L">
    		<a href="{$URL_ROOT}" class="selected-lang">en</a>
    		<i class="fa fa-angle-down"></i>
    		<ul class="select-lang">
    			<a href="{$URL_ROOT}">en</a>
				<a href="{$URL_ROOT}">ru</a>
    			<a href="{$URL_ROOT}">it</a>
    			<a href="{$URL_ROOT}">fr</a>
    			<a href="{$URL_ROOT}">es</a>
    			<a href="{$URL_ROOT}">hi</a>
    			<a href="{$URL_ROOT}">ch</a>

    		</ul>
    	</div>
                <p class="pull-right p_top_header">
            <a href='{$ROOT}register.html'>Register</a>  &nbsp;&nbsp;&nbsp;    <a href='{$ROOT}login.html'>Login</a>  </p>
            
            	
               <div class="social_icons pull-right">
                            
                                        <a href="{$LinksDetails.url_facebook}"
                   class="soc_fb">
                    <i class="fa fa-facebook"></i>
                </a>
                                       <!-- <a href="{$URL_ROOT}https://instagram.com/">
                    <i class="fa fa-instagram"></i>
                </a>-->
                                        <a href="{$LinksDetails.url_twitter}">
                    <i class="fa fa-twitter"></i>
                </a>
                                        <a href="{$LinksDetails.url_google}">
                    <i class="fa fa-google-plus"></i>
                </a>
                    </div>
        
    </div>
</div>                <!-- End row -->
            </div>
            <!-- End container-->
        </div><!-- End top line-->

        

    <div class="container navigate_full">
        <div class="row">
            <div class="col-md-12 clearfix map_header">
                <a href="{$ROOT}" class="menu_xs hidden-md hidden-sm hidden-lg">
                    <i class="fa fa-bars fa-2x"></i>
                </a>
                
                
               
               
                <div class="menu-topheader-container">
                
                <ul id="menu-topheader" class="navigate head_nav">
                
                <li id="nav-menu-item-1727" class="main-menu-itemmenu-item-evenmenu-item-depth-0 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a   href="{$ROOT}">Home</a>
<!--<ul class="sub-menu menu-odd  menu-depth-1">
	<li id="nav-menu-item-7862" class="sub-menu-itemmenu-item-oddmenu-item-depth-1 menu-item menu-item-type-post_type menu-item-object-page"><a   href="{$URL_ROOT}places/index.html">Map &#038; List</a></li>
	<li id="nav-menu-item-7861" class="sub-menu-itemmenu-item-oddmenu-item-depth-1 menu-item menu-item-type-custom menu-item-object-custom"><a   href="{$URL_ROOT}places/index3492.html?showas=minimal">Simple map</a></li>
</ul>-->
</li>




 {foreach from=$menu_header.menu item=abc_menu}
                            <li class="main-menu-itemmenu-item-evenmenu-item-depth-0 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children"><a href="{$abc_menu.url}">{$abc_menu.heading}{if $abc_menu.count>0}<i class="fa fa-angle-down"></i>{/if}</a>
                                {if $abc_menu.count>0}
                                <ul class="sub-menu menu-odd  menu-depth-1">
                                    {foreach from=$abc_menu.menu item=abc_sub_menu}
                                    <li  class="sub-menu-itemmenu-item-oddmenu-item-depth-1 menu-item menu-item-type-post_type menu-item-object-page"><a href="{$abc_sub_menu.url}">{$abc_sub_menu.heading}</a>
                                        {if $abc_sub_menu.count>0}
                                        <ul>
                                            {foreach from=$abc_sub_menu.menu item=abc_sub_sub_menu}
                                            <li><a href="{$abc_sub_sub_menu.url}">{$abc__sub_sub_menu.heading}</a>
                                            </li>
                                                {/foreach}
                                        </ul>
                                        {/if}
                                    </li>					
                                    {/foreach}
                                </ul>
                                {/if}				
                            </li>
                            {/foreach}  
                            

</ul></div>
            </div>
        </div>
    </div>

</header>
       <div class="mycity_o-grid__item mycity_menu-btn ">

     
        
          <a href="{$ROOT}" class="logo">
                    <img
                        src="{$LinksDetails.logo}"
                        alt="">
                    <i class="fa fa-angle-down"></i>
                </a>
        
    </div>