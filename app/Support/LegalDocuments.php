<?php

namespace App\Support;

class LegalDocuments
{
    public const LOCALES = ['ru', 'uk', 'en', 'pl', 'pt', 'ro'];

    public static function isSupported(string $locale): bool
    {
        return in_array(strtolower($locale), self::LOCALES, true);
    }

    public static function languageRule(bool $required = true): string
    {
        $rule = 'string|in:'.implode(',', self::LOCALES);

        return $required ? 'required|'.$rule : 'sometimes|'.$rule;
    }

    /** @return array<string, string> */
    public static function languageOptions(): array
    {
        return [
            'ru' => 'Русский',
            'uk' => 'Українська',
            'en' => 'English',
            'pl' => 'Polski',
            'pt' => 'Português',
            'ro' => 'Română',
        ];
    }

    public static function privacyTitle(string $locale): string
    {
        return match (strtolower($locale)) {
            'pl' => 'Polityka prywatności',
            'pt' => 'Política de privacidade',
            'ro' => 'Politica de confidențialitate',
            default => 'Privacy Policy',
        };
    }

    public static function termsTitle(string $locale): string
    {
        return match (strtolower($locale)) {
            'pl' => 'Regulamin',
            'pt' => 'Termos de utilização',
            'ro' => 'Termeni de utilizare',
            default => 'Terms of Service',
        };
    }

    /** @return array<int, array{section: string, title: string, content: string}> */
    public static function privacySections(string $locale): array
    {
        $locale = strtolower($locale);
        $sections = self::privacyCatalog();

        return $sections[$locale] ?? $sections['en'];
    }

    /** @return array<int, array{section: string, title: string, content: string}> */
    public static function termsSections(string $locale): array
    {
        $locale = strtolower($locale);
        $sections = self::termsCatalog();

        return $sections[$locale] ?? $sections['en'];
    }

    /** @return array<string, array<int, array{section: string, title: string, content: string}>> */
    private static function privacyCatalog(): array
    {
        return [
            'ru' => [
                self::section('dataCollection', 'Сбор данных', 'Мы собираем только необходимую информацию для работы приложения: данные о ваших автомобилях, напоминания, историю обслуживания и основную информацию профиля.'),
                self::section('dataUsage', 'Использование данных', 'Ваши данные используются для предоставления функций приложения, отправки напоминаний и улучшения пользовательского опыта. Мы не продаем ваши данные третьим лицам.'),
                self::section('dataSharing', 'Передача данных', 'Мы не передаем ваши личные данные третьим лицам, за исключением случаев, когда это требуется по закону или для обеспечения безопасности.'),
                self::section('dataSecurity', 'Безопасность данных', 'Мы используем современные методы шифрования и безопасности для защиты ваших данных. Все данные передаются по защищенному соединению HTTPS.'),
                self::section('userRights', 'Права пользователя', 'Вы можете в любое время запросить доступ к своим данным, их изменение или удаление. Для этого обратитесь к нам через форму обратной связи.'),
                self::section('contact', 'Контакты', 'По вопросам конфиденциальности обращайтесь по адресу: privacy@mygarage.uno'),
                self::section('changes', 'Изменения политики', 'Мы можем обновлять эту политику конфиденциальности. О существенных изменениях мы уведомим пользователей через приложение.'),
            ],
            'uk' => [
                self::section('dataCollection', 'Збір даних', 'Ми збираємо тільки необхідну інформацію для роботи додатку: дані про ваші автомобілі, нагадування, історію обслуговування та основну інформацію профілю.'),
                self::section('dataUsage', 'Використання даних', 'Ваші дані використовуються для надання функцій додатку, надсилання нагадувань та покращення користувацького досвіду. Ми не продаємо ваші дані третім особам.'),
                self::section('dataSharing', 'Передача даних', 'Ми не передаємо ваші персональні дані третім особам, за винятком випадків, коли це вимагається законом або для забезпечення безпеки.'),
                self::section('dataSecurity', 'Безпека даних', 'Ми використовуємо сучасні методи шифрування та безпеки для захисту ваших даних. Всі дані передаються по захищеному з\'єднанню HTTPS.'),
                self::section('userRights', 'Права користувача', 'Ви можете в будь-який час запросити доступ до своїх даних, їх зміну або видалення. Для цього зверніться до нас через форму зворотного зв\'язку.'),
                self::section('contact', 'Контакти', 'З питаннями конфіденційності звертайтеся за адресою: privacy@mygarage.uno'),
                self::section('changes', 'Зміни політики', 'Ми можемо оновлювати цю політику конфіденційності. Про істотні зміни ми повідомимо користувачів через додаток.'),
            ],
            'en' => [
                self::section('dataCollection', 'Data Collection', 'We collect only the necessary information for the app to function: data about your vehicles, reminders, service history and basic profile information.'),
                self::section('dataUsage', 'Data Usage', 'Your data is used to provide app features, send reminders and improve user experience. We do not sell your data to third parties.'),
                self::section('dataSharing', 'Data Sharing', 'We do not share your personal data with third parties, except when required by law or for security purposes.'),
                self::section('dataSecurity', 'Data Security', 'We use modern encryption and security methods to protect your data. All data is transmitted over secure HTTPS connection.'),
                self::section('userRights', 'User Rights', 'You can request access to your data, its modification or deletion at any time. To do this, contact us through the feedback form.'),
                self::section('contact', 'Contact', 'For privacy questions, contact us at: privacy@mygarage.uno'),
                self::section('changes', 'Policy Changes', 'We may update this privacy policy. We will notify users about significant changes through the app.'),
            ],
            'pl' => [
                self::section('dataCollection', 'Zbieranie danych', 'Zbieramy tylko informacje niezbędne do działania aplikacji: dane o Twoich pojazdach, przypomnienia, historię serwisu i podstawowe informacje profilu.'),
                self::section('dataUsage', 'Wykorzystanie danych', 'Twoje dane służą do udostępniania funkcji aplikacji, wysyłania przypomnień i poprawy wygody korzystania. Nie sprzedajemy Twoich danych osobom trzecim.'),
                self::section('dataSharing', 'Udostępnianie danych', 'Nie przekazujemy Twoich danych osobowych osobom trzecim, z wyjątkiem przypadków wymaganych przez prawo lub niezbędnych dla bezpieczeństwa.'),
                self::section('dataSecurity', 'Bezpieczeństwo danych', 'Stosujemy nowoczesne metody szyfrowania i zabezpieczeń, aby chronić Twoje dane. Wszystkie dane są przesyłane przez zabezpieczone połączenie HTTPS.'),
                self::section('userRights', 'Prawa użytkownika', 'W każdej chwili możesz poprosić o dostęp do swoich danych, ich zmianę lub usunięcie. Skontaktuj się z nami przez formularz kontaktowy.'),
                self::section('contact', 'Kontakt', 'W sprawach prywatności napisz na adres: privacy@mygarage.uno'),
                self::section('changes', 'Zmiany polityki', 'Możemy aktualizować tę politykę prywatności. O istotnych zmianach poinformujemy użytkowników w aplikacji.'),
            ],
            'pt' => [
                self::section('dataCollection', 'Recolha de dados', 'Recolhemos apenas a informação necessária para o funcionamento da aplicação: dados sobre os seus veículos, lembretes, histórico de manutenção e informação básica do perfil.'),
                self::section('dataUsage', 'Utilização dos dados', 'Os seus dados são utilizados para disponibilizar as funcionalidades da aplicação, enviar lembretes e melhorar a experiência. Não vendemos os seus dados a terceiros.'),
                self::section('dataSharing', 'Partilha de dados', 'Não partilhamos os seus dados pessoais com terceiros, exceto quando a lei o exige ou por motivos de segurança.'),
                self::section('dataSecurity', 'Segurança dos dados', 'Utilizamos métodos modernos de encriptação e segurança para proteger os seus dados. Todos os dados são transmitidos através de uma ligação HTTPS segura.'),
                self::section('userRights', 'Direitos do utilizador', 'Pode pedir acesso aos seus dados, a sua alteração ou eliminação a qualquer momento. Para isso, contacte-nos através do formulário de contacto.'),
                self::section('contact', 'Contacto', 'Para questões de privacidade, contacte-nos em: privacy@mygarage.uno'),
                self::section('changes', 'Alterações à política', 'Podemos atualizar esta política de privacidade. Notificaremos os utilizadores sobre alterações significativas através da aplicação.'),
            ],
            'ro' => [
                self::section('dataCollection', 'Colectarea datelor', 'Colectăm doar informațiile necesare pentru funcționarea aplicației: date despre vehiculele tale, mementouri, istoricul de service și informațiile de bază ale profilului.'),
                self::section('dataUsage', 'Utilizarea datelor', 'Datele tale sunt folosite pentru a oferi funcțiile aplicației, a trimite mementouri și a îmbunătăți experiența. Nu vindem datele tale către terți.'),
                self::section('dataSharing', 'Partajarea datelor', 'Nu transmitem datele tale personale către terți, exceptând cazurile cerute de lege sau necesare pentru securitate.'),
                self::section('dataSecurity', 'Securitatea datelor', 'Folosim metode moderne de criptare și securitate pentru a-ți proteja datele. Toate datele sunt transmise printr-o conexiune HTTPS securizată.'),
                self::section('userRights', 'Drepturile utilizatorului', 'Poți solicita oricând accesul la datele tale, modificarea sau ștergerea lor. Pentru aceasta, contactează-ne prin formularul de feedback.'),
                self::section('contact', 'Contact', 'Pentru întrebări despre confidențialitate, scrie-ne la: privacy@mygarage.uno'),
                self::section('changes', 'Modificări ale politicii', 'Putem actualiza această politică de confidențialitate. Vom anunța utilizatorii despre modificările importante prin aplicație.'),
            ],
        ];
    }

    /** @return array<string, array<int, array{section: string, title: string, content: string}>> */
    private static function termsCatalog(): array
    {
        return [
            'ru' => [
                self::section('acceptance', 'Принятие условий', 'Используя приложение myGarage, вы соглашаетесь с настоящими Условиями использования. Если вы не согласны с этими условиями, пожалуйста, не используйте приложение.'),
                self::section('subscriptions', 'Подписки', 'Приложение предлагает автоматически продлеваемые подписки. Подписки автоматически продлеваются, если не отменить их за 24 часа до окончания периода. Оплата производится через ваш аккаунт Apple ID или Google Play.'),
                self::section('cancellation', 'Отмена подписки', 'Вы можете отменить подписку в любое время через настройки вашего Apple ID или Google Play. После отмены доступ к функциям подписки сохранится до конца оплаченного периода.'),
                self::section('userContent', 'Контент пользователя', 'Вы несете ответственность за весь контент, который вы загружаете в приложение. Мы не претендуем на права собственности на ваш контент.'),
                self::section('prohibitedUse', 'Запрещенное использование', 'Запрещается использовать приложение для незаконных целей, нарушения прав других лиц или распространения вредоносного контента.'),
                self::section('limitation', 'Ограничение ответственности', 'Приложение предоставляется "как есть". Мы не гарантируем бесперебойную работу приложения и не несем ответственности за любые убытки, возникшие в результате использования приложения.'),
                self::section('changes', 'Изменения условий', 'Мы оставляем за собой право изменять эти условия в любое время. О существенных изменениях мы уведомим пользователей через приложение.'),
                self::section('contact', 'Контакты', 'По вопросам использования приложения обращайтесь по адресу: support@mygarage.uno'),
            ],
            'uk' => [
                self::section('acceptance', 'Прийняття умов', 'Використовуючи додаток myGarage, ви погоджуєтеся з цими Умовами використання. Якщо ви не згодні з цими умовами, будь ласка, не використовуйте додаток.'),
                self::section('subscriptions', 'Підписки', 'Додаток пропонує автоматично відновлювані підписки. Підписки автоматично продовжуються, якщо не скасувати їх за 24 години до закінчення періоду. Оплата здійснюється через ваш обліковий запис Apple ID або Google Play.'),
                self::section('cancellation', 'Скасування підписки', 'Ви можете скасувати підписку в будь-який час через налаштування вашого Apple ID або Google Play. Після скасування доступ до функцій підписки збережеться до кінця оплаченого періоду.'),
                self::section('userContent', 'Контент користувача', 'Ви несете відповідальність за весь контент, який ви завантажуєте в додаток. Ми не претендуємо на права власності на ваш контент.'),
                self::section('prohibitedUse', 'Заборонене використання', 'Заборонено використовувати додаток для незаконних цілей, порушення прав інших осіб або поширення шкідливого контенту.'),
                self::section('limitation', 'Обмеження відповідальності', 'Додаток надається "як є". Ми не гарантуємо безперебійну роботу додатку і не несемо відповідальності за будь-які збитки, що виникли в результаті використання додатку.'),
                self::section('changes', 'Зміни умов', 'Ми залишаємо за собою право змінювати ці умови в будь-який час. Про істотні зміни ми повідомимо користувачів через додаток.'),
                self::section('contact', 'Контакти', 'З питаннями використання додатку звертайтеся за адресою: support@mygarage.uno'),
            ],
            'en' => [
                self::section('acceptance', 'Acceptance of Terms', 'By using the myGarage app, you agree to these Terms of Service. If you do not agree with these terms, please do not use the app.'),
                self::section('subscriptions', 'Subscriptions', 'The app offers auto-renewing subscriptions. Subscriptions automatically renew unless cancelled at least 24 hours before the end of the period. Payment is charged through your Apple ID or Google Play account.'),
                self::section('cancellation', 'Cancellation', 'You can cancel your subscription at any time through your Apple ID or Google Play settings. After cancellation, access to subscription features will remain until the end of the paid period.'),
                self::section('userContent', 'User Content', 'You are responsible for all content you upload to the app. We do not claim ownership of your content.'),
                self::section('prohibitedUse', 'Prohibited Use', 'It is prohibited to use the app for illegal purposes, violation of others\' rights, or distribution of harmful content.'),
                self::section('limitation', 'Limitation of Liability', 'The app is provided "as is". We do not guarantee uninterrupted operation of the app and are not liable for any losses arising from the use of the app.'),
                self::section('changes', 'Changes to Terms', 'We reserve the right to change these terms at any time. We will notify users about significant changes through the app.'),
                self::section('contact', 'Contact', 'For questions about using the app, contact us at: support@mygarage.uno'),
            ],
            'pl' => [
                self::section('acceptance', 'Akceptacja warunków', 'Korzystając z aplikacji myGarage, akceptujesz niniejszy regulamin. Jeśli się z nim nie zgadzasz, nie korzystaj z aplikacji.'),
                self::section('subscriptions', 'Subskrypcje', 'Aplikacja oferuje subskrypcje odnawiane automatycznie. Odnawiają się one, jeśli nie anulujesz ich co najmniej 24 godziny przed końcem okresu. Płatność jest pobierana przez konto Apple ID lub Google Play.'),
                self::section('cancellation', 'Anulowanie subskrypcji', 'Subskrypcję możesz anulować w dowolnym momencie w ustawieniach Apple ID lub Google Play. Po anulowaniu dostęp do funkcji subskrypcji pozostaje do końca opłaconego okresu.'),
                self::section('userContent', 'Treści użytkownika', 'Odpowiadasz za wszystkie treści, które przesyłasz do aplikacji. Nie rościmy sobie praw do Twoich treści.'),
                self::section('prohibitedUse', 'Zabronione użycie', 'Zabronione jest używanie aplikacji do celów niezgodnych z prawem, naruszania praw innych osób lub rozpowszechniania szkodliwych treści.'),
                self::section('limitation', 'Ograniczenie odpowiedzialności', 'Aplikacja jest udostępniana „tak jak jest”. Nie gwarantujemy nieprzerwanego działania i nie odpowiadamy za szkody powstałe w wyniku korzystania z aplikacji.'),
                self::section('changes', 'Zmiany warunków', 'Zastrzegamy sobie prawo do zmiany tych warunków w dowolnym momencie. O istotnych zmianach poinformujemy użytkowników w aplikacji.'),
                self::section('contact', 'Kontakt', 'W sprawach dotyczących korzystania z aplikacji napisz na adres: support@mygarage.uno'),
            ],
            'pt' => [
                self::section('acceptance', 'Aceitação dos termos', 'Ao utilizar a aplicação myGarage, aceita estes Termos de utilização. Se não concordar com estes termos, não utilize a aplicação.'),
                self::section('subscriptions', 'Subscrições', 'A aplicação oferece subscrições com renovação automática. As subscrições renovam-se automaticamente, salvo se forem canceladas pelo menos 24 horas antes do fim do período. O pagamento é efetuado através da conta Apple ID ou Google Play.'),
                self::section('cancellation', 'Cancelamento', 'Pode cancelar a subscrição a qualquer momento nas definições do Apple ID ou do Google Play. Após o cancelamento, o acesso às funcionalidades da subscrição mantém-se até ao fim do período pago.'),
                self::section('userContent', 'Conteúdo do utilizador', 'É responsável por todo o conteúdo que carrega na aplicação. Não reclamamos a propriedade do seu conteúdo.'),
                self::section('prohibitedUse', 'Utilização proibida', 'É proibido utilizar a aplicação para fins ilegais, para violar os direitos de terceiros ou para distribuir conteúdo prejudicial.'),
                self::section('limitation', 'Limitação de responsabilidade', 'A aplicação é fornecida "tal como está". Não garantimos o funcionamento ininterrupto e não nos responsabilizamos por quaisquer perdas resultantes da utilização da aplicação.'),
                self::section('changes', 'Alterações aos termos', 'Reservamo-nos o direito de alterar estes termos a qualquer momento. Notificaremos os utilizadores sobre alterações significativas através da aplicação.'),
                self::section('contact', 'Contacto', 'Para questões sobre a utilização da aplicação, contacte-nos em: support@mygarage.uno'),
            ],
            'ro' => [
                self::section('acceptance', 'Acceptarea termenilor', 'Folosind aplicația myGarage, ești de acord cu acești Termeni de utilizare. Dacă nu ești de acord cu acești termeni, nu folosi aplicația.'),
                self::section('subscriptions', 'Abonamente', 'Aplicația oferă abonamente cu reînnoire automată. Abonamentele se reînnoiesc automat dacă nu sunt anulate cu cel puțin 24 de ore înainte de sfârșitul perioadei. Plata se face prin contul Apple ID sau Google Play.'),
                self::section('cancellation', 'Anulare', 'Poți anula abonamentul oricând din setările Apple ID sau Google Play. După anulare, accesul la funcțiile abonamentului rămâne până la sfârșitul perioadei plătite.'),
                self::section('userContent', 'Conținutul utilizatorului', 'Ești responsabil pentru tot conținutul pe care îl încarci în aplicație. Nu revendicăm proprietatea asupra conținutului tău.'),
                self::section('prohibitedUse', 'Utilizare interzisă', 'Este interzisă folosirea aplicației în scopuri ilegale, pentru încălcarea drepturilor altor persoane sau pentru distribuirea de conținut dăunător.'),
                self::section('limitation', 'Limitarea răspunderii', 'Aplicația este furnizată „ca atare”. Nu garantăm funcționarea neîntreruptă și nu răspundem pentru pierderile rezultate din utilizarea aplicației.'),
                self::section('changes', 'Modificarea termenilor', 'Ne rezervăm dreptul de a modifica acești termeni în orice moment. Vom anunța utilizatorii despre modificările importante prin aplicație.'),
                self::section('contact', 'Contact', 'Pentru întrebări despre utilizarea aplicației, scrie-ne la: support@mygarage.uno'),
            ],
        ];
    }

    /** @return array{section: string, title: string, content: string} */
    private static function section(string $section, string $title, string $content): array
    {
        return [
            'section' => $section,
            'title' => $title,
            'content' => $content,
        ];
    }
}
