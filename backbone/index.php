<!doctype html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" href="/backbone/public/bootstrap/css/bootstrap.min.css">
</head>
<body>

<div class='container'>
<h1>A Simple Crud using backbone.js + Slim REST + Red Beans PHP</h1>
<hr />
<div class='page'></div>
</div>

<script type='text/template' id='article-list-template'>
<h4></h4>
<a href='#/new' class='btn btn-primary'>New Article</a>
<hr />
<table class='table striped'>
   <thead>
       <tr>
            <td>Article Title</td>
            <td>Article Url</td>
            <td></td>
       </tr>
   </thead>       
       <tbody>
       <% _.each(articles , function(article) { %>
       <tr>
            <td><%= article.get('article_title') %></td>
            <td><a href='#'><%= article.get('article_url') %></a></td>
            <td><a href="#/edit/<%= article.get('id') %>" class='btn'>Edit</a></td>
       </tr>
       <% }); %>
   </tbody>
</table>

</script>

<script type='text/template' id='edit-list-template'>
<form class='edit-article-form'>
    <legend><%= article ? 'Update' : 'Create' %>Article</legend>
    
    <label>Article Title</label>
    <input type='text' name='article_title' value="<%= article ? article.article_title : '' %>">

    <label>Article Url</label>
    <input type='text' name='article_url' value="<%= article ? article.article_url : '' %>">
    <hr />

    <% if(article) { %>
        <input type='hidden' name="id" value="<%= article.id %>">    
    <% } %>

    <button type='submit' class='btn' value='Submit'><%= article ? 'Update' : 'Create' %></button>
</form>
</script>

<script src="/backbone/public/js/jquery-1.11.0.min.js"></script>
<script src="/backbone/public/js/underscore.js"></script>
<script src="/backbone/public/js/backbone-min.js"></script>
<script>
    $.ajaxPrefilter(function( options ) {
        options.url = "http://localhost/restcrud" +  options.url ;
    });

    //http://forum.jquery.com/topic/serializeobject
    $.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
    };

    var Articles = Backbone.Collection.extend({
    	url: '/articles'
    });

    var Article = Backbone.Model.extend({
        urlRoot: '/articles'
    });

    var EditArticle = Backbone.View.extend({
        el : '.page' ,
        render : function(options) {
            var that = this; 
            if(options.id) {
                 var article = new Article({id: options.id});
                 article.fetch({
                    success : function (article) {
                        //var parsedResponse = JSON.parse( response );
                        //article = JSON.parse(article);
                        //alert(JSON.stringify(article));
                        //console.log(article.attributes[0].article_date);
                        var article = article.attributes[0];
                        //alert(article.article_title);
                        var template = _.template($('#edit-list-template').html() , {article : article});
                        that.$el.html(template);           
                    }   
                });
            }else {
                var template = _.template($('#edit-list-template').html() , {article : null });
                this.$el.html(template);
            }     
        },
        events: {
            'submit .edit-article-form' : 'saveArticle',     
        },
        saveArticle : function(ev) {
            var articleDetails = $(ev.currentTarget).serializeObject();
            var article = new Article();
            article.save(articleDetails , {
                success : function(data) {
                     
                }
            });
            route.navigate('' , {trigger : true });
            return false;  
        }
    });

    var UserList = Backbone.View.extend({
        el : '.page',
        render :  function() {
            var that = this;
            var articles = new Articles;
            articles.fetch({
                success: function (articles) {
                    var template = _.template($('#article-list-template').html(), { articles: articles.models});
                    that.$el.html(template);    
                }     
            })	
        }
    });

    var Router = Backbone.Router.extend({
         routes : {
         	'' : 'home' ,
            'new' : 'editArticle',
            'edit/:id' :'editArticle'
         }
    });
    
    var route = new Router();
  
    var userlist = new UserList(); 
    route.on('route:home' , function(){
     	userlist.render();
    });
    
    var editArticle = new EditArticle();    
    route.on('route:editArticle' , function(id){
        editArticle.render( { id : id } );
    });

    Backbone.history.start();
</script>
</body>
</html>