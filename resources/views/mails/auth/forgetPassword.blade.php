<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">{{ $name }},</p><br>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">wir haben eine Anfrage zum Zurücksetzen Ihres Passworts erhalten. Wenn Sie diese Anfrage nicht gestellt haben, können Sie diese E-Mail ignorieren.</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Um Ihr Passwort zurückzusetzen, folgen Sie bitte diesem Link: [<a href="https://new.sevendisplays.com/{{$language!='de'? $language.'/':''}}reset-password?token={{$token}}">Link zur Passwortzurücksetzung</a>]</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Dieser Link ist aus Sicherheitsgründen nur für kurze Zeit gültig. Sollten Sie Probleme beim Zurücksetzen Ihres Passworts haben, zögern Sie nicht, uns zu kontaktieren.</p><br>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Mit freundlichen Grüßen,</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Ihr sevendisplays Team</p>
    <br>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">SEVEN displays GmbH</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Robert-Perthel-Straße 12c</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">50739 Köln</p>
    <br>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Tel:  0221-22204588</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Fax: 0221-22204668</p>
    <br>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;"><b>E-Mail:</b> info@sevendisplays.com</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;"><b>Internet:</b> www.sevendisplays.com</p>
    <br>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Geschäftsführung: Hotan Bakhtiari Davijani (Dipl.-Kfm.)</p>
    <br>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;"><b>Handelsregisternummer:</b> HRB 69190</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Finanzamt Köln Nord</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;"><b>Steuernummer:</b> 217/5783/1070</p>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;"><b>UST-IDNR:</b> DE 272188711 </p>
    <br>
    <p style="margin:0;font-size:15px;line-height: 18px;font-family:Arial,sans-serif;">Diese E-Mail enthält vertrauliche und/oder rechtlich geschützte Informationen.
    Wenn Sie nicht der richtige Adressat sind oder diese E-Mail irrtümlich erhalten
    haben, informieren Sie bitte sofort den Absender und vernichten Sie diese Mail.</p>
</body>
</html>
