var $$ = Dom7;

var usuario = 0;

var app = new Framework7({
  root: '#app', // App root element


  name: 'Rede Social Etec', // App name
  theme: 'auto', // Automatic theme detection

  // App root data
  data: function () {
    return {
      user: {
        firstName: 'John',
        lastName: 'Doe',
      },

    };
  },
  // App root methods
  methods: {
    helloWorld: function () {
      app.dialog.alert('Hello World!');
    },
  },
  // App routes
  routes: routes,
  // Register service worker
  serviceWorker: {
    path: '/service-worker.js',
  },
});
// Login Screen Demo
$$('#my-login-screen .login-button').on('click', function () {
  var username = $$('#my-login-screen [name="username"]').val();
  var password = $$('#my-login-screen [name="password"]').val();

  app.request.post('https://aluno.etecarmine.com.br/3DS/QTS/login.php', {
      login: username,
      senha: password
    },
    function (data) {
      var result = JSON.parse(data);
      if (result.status == 200) {
        //app.dialog.alert("Bem vindo " + result.usuario[0].tb01_nome);
        usuario = result.usuario[0].tb01_id;
        app.views.main.router.navigate('/feed/', {
          reloadCurrent: true
        });
      } else {
        app.dialog.alert(result.erro);
      }

    });
});
// botao cadastrar
function cadastrar() {
  var nome = $$('#nome').val();
  var senha = $$('#senha').val();
  var celular = $$('#celular').val();
  var email = $$('#email').val();
  var usuario = $$('#usuario').val();

  app.request.post('https://aluno.etecarmine.com.br/3DS/QTS/cadastro.php', {
      nome: nome,
      senha: senha,
      celular: celular,
      email: email,
      usuario: usuario
    },
    function (data) {
      app.dialog.alert(data.mensagem);
      app.views.main.router.navigate('/', {
        reloadCurrent: true
      });
    });
};

function postar() {
  //var foto = $$('#foto').val();
  //var texto = $$('#texto').val();

  var texto = $$("#texto").html();

  var foto=document.getElementById('foto').files[0];

	var FData = new FormData();
  FData.append('foto',foto);
  FData.append('usuario',usuario);  
  FData.append('texto',texto);  

  app.request({
    url: 'https://aluno.etecarmine.com.br/3DS/QTS/postagem_incluir.php', 
    method: 'POST', 
    contentType: "multipart/form-data",
    data: FData,
    success: function (data) {
      //app.dialog.alert(data.mensagem);
      app.views.main.router.navigate('/feed/', {
        reloadCurrent: true
      });
    }
  });
};

function curtir(post) {

  app.request.post('https://aluno.etecarmine.com.br/3DS/QTS/curtidas_incluir.php', {
      usuario: usuario,
      postagem: post
    },
    function (data) {
      //app.dialog.alert(data.mensagem);
      app.views.main.router.navigate('/feed/', {
        reloadCurrent: true
      });
    });
};

function comentar(post) {

  app.dialog.prompt('Digite seu comentário:', function (comentario) {

    app.request.post('https://aluno.etecarmine.com.br/3DS/QTS/comentario_incluir.php', {
      usuario: usuario,
      postagem: post,
      texto: comentario
    },
    function (data) {
      //app.dialog.alert(data.mensagem);
      app.views.main.router.navigate('/feed/', {
        reloadCurrent: true
      });
    });

  });
  

};



$$(document).on('page:afterin', '.page[data-name="feed"]', function (e) {
  app.request.post('https://aluno.etecarmine.com.br/3DS/QTS/postagem_listar.php',{
    usuario: usuario
  },
  function (data) {
    var result = JSON.parse(data);

    for (var posicao in result.mensagem) {
      var card = "";
      card = card + '  <div class="card demo-facebook-card">'
      card = card + '      <div class="card-header">'
      card = card + '          <div class="demo-facebook-name">' + result.mensagem[posicao].tb01_nome + '</div>'
      card = card + '          <div class="demo-facebook-date">' + result.mensagem[posicao].tb02_data + '</div>'
      card = card + '      </div>'
      card = card + '      <div class="card-content card-content-padding">'
      card = card + '          <p>' + result.mensagem[posicao].tb02_texto + '</p>'
      card = card + '          <img src="https://aluno.etecarmine.com.br/3DS/QTS/fotos/' + result.mensagem[posicao].tb02_foto + '" width="100%" />'
      card = card + '      <br>' + result.mensagem[posicao].comentarios_texto
      card = card + '          <p class="likes">Curtidas: ' + result.mensagem[posicao].curtidas + ' &nbsp;&nbsp; Comentários: ' + result.mensagem[posicao].comentarios + '</p>'
      card = card + '      </div>'
      card = card + '      <div class="card-footer">'
      card = card + '  <a href="#" class="link" onclick="curtir(' + result.mensagem[posicao].tb02_id + ')">Curtir</a>'
      card = card + '  <a href="#" class="link" onclick="comentar(' + result.mensagem[posicao].tb02_id + ')">Comentar</a></div>'
      card = card + '  </div>'
      $$('#lista').append(card);
    }
  });

  app.request.post('https://aluno.etecarmine.com.br/3DS/QTS/usuarios_listar.php',{
    usuario: usuario
  },
  function (data) {
    var result = JSON.parse(data);

    for (var posicao in result.mensagem) {
      var card = "";
      card = card + '  <div class="card demo-facebook-card">'
      card = card + '      <div class="card-header">'
      card = card + '          <div class="demo-facebook-name">' + result.mensagem[posicao].tb01_nome + '</div>'
      card = card + '          <div class="demo-facebook-date">' + result.mensagem[posicao].tb01_usuario + '</div>'
      card = card + '      </div>'
      card = card + '      <div class="card-footer">'
      if(result.mensagem[posicao].convite_recebido == 0) {
        card = card + '  <a href="#" class="link" onclick="aceitar(' + result.mensagem[posicao].tb01_id + ')">Aceitar</a>'
        card = card + '  <a href="#" class="link" onclick="ignorar(' + result.mensagem[posicao].tb01_id + ')">Ignorar</a>'
      } else {
        if(result.mensagem[posicao].convite_feito == -1) {
          card = card + '  <a href="#" class="link" onclick="convidar(' + result.mensagem[posicao].tb01_id + ')">Convidar</a>'
        } else {
          card = card + '  <a href="#" class="link">Aguardando resposta</a>'
        }
      }
      card = card + '  </div>'
      $$('#listaamigos').append(card);
    }
  });

});


function convidar(amigo) {
  app.request.post('https://aluno.etecarmine.com.br/3DS/QTS/amigos_incluir.php', {
      usuario: usuario,
      amigo: amigo
    },
    function (data) {
      //app.dialog.alert(data.mensagem);
      app.views.main.router.navigate('/feed/', {
        reloadCurrent: true
      });
    });
};

function aceitar(amigo) {
  app.request.post('https://aluno.etecarmine.com.br/3DS/QTS/amigos_aceitar.php', {
      usuario: usuario,
      amigo: amigo
    },
    function (data) {
      //app.dialog.alert(data.mensagem);
      app.views.main.router.navigate('/feed/', {
        reloadCurrent: true
      });
    });
};

function ignorar(amigo) {
  app.request.post('https://aluno.etecarmine.com.br/3DS/QTS/amigos_ignorar.php', {
      usuario: usuario,
      amigo: amigo
    },
    function (data) {
      //app.dialog.alert(data.mensagem);
      app.views.main.router.navigate('/feed/', {
        reloadCurrent: true
      });
    });
};
