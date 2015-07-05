<!-- include Google hosted jQuery Library -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<!-- Start jQuery code -->
<script type="text/javascript">
$(document).ready(function() {
    $("#submit_btn").click(function() { 
       
        var proceed = true;
       
        if(proceed) //everything looks good! proceed...
        {
            //get input field values data to be sent to server
            post_data = {
                'user_name'     : $('input[name=name]').val(), 
                'user_age'    : $('input[name=age]').val()
            };
            
            //Ajax post data to server
            $.post('process.php', post_data, function(response){  
                if(response.type == 'error'){ //load json data from server and output message     
                    output = '<div class="error">'+response.text+'</div>';
                }else{
                    output = '<div class="success">'+response.text+'</div>';
                    //reset values in all input fields
                    $("#my_form input[type=text]").val(''); 
                    //$("#my_form #form_body").slideUp(); //hide form after success
                }
                $("#my_form #form_results").html(output);
            }, 'json');
        }
    });
	
});
</script>

<div class="form-style" id="my_form">
    <div id="form_results"></div>
    <div id="form_body">
        <label><span>Name</span>
            <input type="text" name="name" id="name" class="input-field"/>
        </label>
        <label><span>Age</span>
            <input type="text" name="age" class="input-field"/>
        </label>
        <label>
            <span>&nbsp;</span><input type="submit" id="submit_btn" value="Submit" />
        </label>
    </div>
</div>