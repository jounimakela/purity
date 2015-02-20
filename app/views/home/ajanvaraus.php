<!DOCTYPE html>
<html lang="fi">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kätevät Käpälät ...">
    <meta name="author" content="Saana Mäkelä">

    <title>Kätevät Käpälät | Ajanvaraus</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Custom styles for this template -->
    <link rel="stylesheet" type="text/css" href="css/tassu.css">
    <link rel="stylesheet" type="text/css" href="css/sweet-alert.css">
    <link rel="stylesheet" type="text/css" href="css/reservation.css">
    <link rel="stylesheet" type="text/css" href="css/calendar.css">
  </head>

  <body>
    <div class="container">
      <div class="row">
        <div class="container-box col-lg-8 col-lg-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
          <!-- Header -->
          <div class="header">
            <div id="calendar-title" class="col-sm-4 activated"><h4 class="text-center">Ajanvaraus</h4></div>
            <div id="contact-title" class="col-sm-4 hidden-xs"><h4 class="text-center">Yhteystiedot</h4></div>
            <div id="confirm-title" class="col-sm-4 hidden-xs"><h4 class="text-center">Vahvistus</h4></div>
          </div>

          <div id="calendar-section" class="content">
            <p class="text-center">Valitse kalenterista vapaa aika. Päivät, jotka ovat vapaita, ovat merkitty harmailla neliöillä päivämäärän alapuolella. Klikkaa päivää nähdäksesi ajanvarauksen kellonajat.</p>
            <p id="reserve" class="text-center"><i>Ei valittua aikaa!</i></p>

            <!-- Calendar -->
            <section class="calendar-section">
              <div class="custom-calendar-wrap">
                <div id="custom-inner" class="custom-inner">
                  <div class="custom-header clearfix">
                    <nav>
                      <span id="custom-prev" class="custom-prev"></span>
                      <span id="custom-next" class="custom-next"></span>
                    </nav>
                    <h2 id="custom-month" class="custom-month"></h2>
                    <h3 id="custom-year" class="custom-year"></h3>
                  </div>
                  <div id="calendar" class="fc-calendar-container"></div>
                </div>
              </div>
            </section>

            <div class="footer col-md-12">
              <a href="#" class="btn-prev btn btn-link pull-left disabled">Edellinen</a>
              <a href="#" class="btn-next btn btn-link pull-right">Seuraava</a>
            </div>
          </div>

          <div id="contact-section" class="hidden content">
            <p class="text-center">Täytä yhteystietosi ja kerro lisätiedoissa lyhyesti koirasi vaivasta. Kaikki kentät ovat pakollisia.</p>

            <form id="contact-form" class="form-horizontal">
              <div class="form-group">
                <label for="firstname" class="col-sm-2 control-label">Etunimi</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Etunimi"
                   onkeydown="clearWarnings($(this));">
                </div>
              </div>
              <div class="form-group">
                <label for="lastname" class="col-sm-2 control-label">Sukunimi</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Sukunimi"
                   onkeydown="clearWarnings($(this));">
                </div>
              </div>
              <div class="form-group">
                <label for="email" class="col-sm-2 control-label">Sähköposti</label>
                <div class="col-sm-10">
                  <input type="email" class="form-control" id="email" name="email" placeholder="Sähköposti"
                   onkeydown="clearWarnings($(this));">
                </div>
              </div>
              <div class="form-group">
                <label for="phone" class="col-sm-2 control-label">Puhelinnumero</label>
                <div class="col-sm-10">
                  <input type="tel" class="form-control" id="phone" name="phone" placeholder="Puhelinnumero"
                   onkeydown="clearWarnings($(this));">
                </div>
              </div>
              <div class="form-group">
                <label for="message" class="col-sm-2 control-label">Lisätietoja</label>
                <div class="col-sm-10">
                  <textarea id="message" class="form-control" name="message" rows="3" onkeydown="clearWarnings($(this));"></textarea>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="newsletter">Haluan tilata Kätevät Käpälät-uutiskirjeen
                    </label>
                  </div>
                </div>
              </div>
              <div class="footer col-md-12">
                <a href="#" class="btn-prev btn btn-link pull-left">Edellinen</a>
                <a href="#" class="btn-next btn btn-link pull-right">Varaa aika!</a>
              </div>
              <input type="hidden" name="token" value="<?php echo $token; ?>">
            </form>
          </div>

          <div id="confirm-section" class="hidden content text-center">
            <h1>Kiitos varauksesta!</h1>
            <h4>Vielä yksi juttu..</h4>
            <br />
            <p>Ajanvaraus tulee vahvistaa ennen sen voimaanastumista. Tarkasta sähköpostisi ja klikkaa siellä olevaa vahvistuslinkkiä, jotta ajanvaraus tallentuu järjestelmään.</p>
            <p>Aika on varattuna teille 24 tunnin ajan, jonka jälkeen se vapautuu, jos aikaa ei ole vahvistettu.</p>
            <br />
            <a href="/"><h3>Palaa etusivulle!</h3></a>
          </div>
        </div>
      </div>
      <div id="links" class="text-center">
        <a href="/">katevatkapalat.com</a>
      </div>

    </div>

    <!-- Javascript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/modernizr.custom.63321.js"></script>
    <script src="js/smooth-scroll.js"></script>
    <script src="js/jquery.calendario.js"></script>
    <script src="js/sweet-alert.min.js"></script>
    <script src="js/calendar.js"></script>
    <script src="js/navigation.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>