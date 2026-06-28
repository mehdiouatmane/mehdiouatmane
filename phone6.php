<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>AuraTech AI - المرجع الشامل للتكنولوجيا</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/react@18/umd/react.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js" crossorigin></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark: { 900: '#09090b', 800: '#18181b', 700: '#27272a' },
                        brand: { primary: '#3b82f6', secondary: '#8b5cf6', accent: '#06b6d4' }
                    },
                    fontFamily: { cairo: ['Cairo', 'sans-serif'] },
                    animation: { 'fade-in': 'fadeIn 0.3s ease-out', 'slide-up': 'slideUp 0.4s ease-out' },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideUp: { '0%': { transform: 'translateY(15px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #09090b; color: white; margin: 0; padding: 0; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .glass-panel { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body>

<div id="root"></div>

<script type="text/babel">
    const { useState } = React;

    // ==========================================
    // 1. قواعد البيانات الشاملة
    // ==========================================

    // قاعدة بيانات المتاجر
    const storesDB = [
        { id: 1, name: "Disway & Disty (استيراد)", type: "رسمي بالجملة", phone: "0522-111111", email: "contact@disway.ma", map: "https://maps.google.com", social: "@disway_maroc", desc: "المستورد الرسمي للحواسيب بالمغرب." },
        { id: 2, name: "Derb Ghallef Hub", type: "سوق موازي (Grey)", phone: "0600-222222", email: "info@derbhub.ma", map: "https://maps.google.com", social: "@derb_tech", desc: "أكبر تجمع للإلكترونيات المستوردة من دبي (بدون ضمان الوكالة)." },
        { id: 3, name: "AzzamPhone", type: "تجزئة وجملة", phone: "0611-333333", email: "sales@azzam.ma", map: "https://maps.google.com", social: "@azzamphone", desc: "متخصص في الهواتف الذكية بأسعار الجملة." },
        { id: 4, name: "iStyle / Virgin", type: "تجزئة رسمي", phone: "0522-444444", email: "store@istyle.ma", map: "https://maps.google.com", social: "@istyle_ma", desc: "الوكيل الرسمي لآبل، ضمان 100% مع أسعار مرتفعة." }
    ];

    // قاعدة بيانات الأسعار
    const pricesDB = [
        { device: "iPhone 15 Pro Max 256GB", official: "15,500 DH", grey: "11,800 DH", black: "8,500 DH (Blacklisted/Bypass)", wholesale: "11,000 DH" },
        { device: "Samsung Galaxy S24 Ultra", official: "14,000 DH", grey: "10,500 DH", black: "7,000 DH (Knox Tripped)", wholesale: "9,800 DH" },
        { device: "Lenovo Legion Pro 7i (RTX 4080)", official: "32,000 DH", grey: "26,000 DH", black: "غير متوفر", wholesale: "24,500 DH" }
    ];

    // قاعدة بيانات الفحص (المحقق الميداني)
    const inspectionDB = {
        phone: [
            { step: "فحص الهوية (IMEI)", action: "ركب *#06# وقارن الرقم مع العلبة وظهر الهاتف. ثم ادخل الرقم في موقع imei.info", checks: [{ result: "مطابق ومسجل في الموقع", verdict: "safe", msg: "هوية سليمة" }, { result: "غير مطابق أو غير موجود", verdict: "danger", msg: "هاتف مزور، مسروق، أو اللوحة الأم مستبدلة!" }] },
            { step: "فحص شاشة اللمس", action: "في سامسونج ركب *#0*# واختر Touch. في آيفون اسحب أيقونة على كامل الشاشة.", checks: [{ result: "استجابة سريعة في كل الزوايا", verdict: "safe", msg: "الشاشة أصلية وسليمة" }, { result: "تأخر في اللمس أو بقع ميتة", verdict: "danger", msg: "شاشة مقلدة (LCD) أو بها عيب مصنعي." }] },
            { step: "فحص الحماية (FRP / iCloud)", action: "ادخل للإعدادات، حاول إضافة حسابك، ثم اعمل إعادة تشغيل (Restart).", checks: [{ result: "الجهاز يفتح عادي بدون طلب حساب قديم", verdict: "safe", msg: "الجهاز نظيف" }, { result: "يطلب حساب المالك السابق أو Bypass", verdict: "danger", msg: "مغلق FRP/iCloud، لا تشتريه!" }] }
        ],
        pc: [
            { step: "فحص المعالج والحرارة", action: "حمل برنامج HWiNFO64 وشغل ضغط CPU-Z لمدة 5 دقائق.", checks: [{ result: "الحرارة تحت 85° ولا يوجد Throttling", verdict: "safe", msg: "تبريد ممتاز ومعالج سليم" }, { result: "الحرارة فوق 95° وينطفئ الجهاز", verdict: "danger", msg: "مشكل في اللوحة الأم أو المعالج تالف." }] },
            { step: "فحص القرص الصلب (SSD)", action: "شغل برنامج CrystalDiskInfo وشاهد النسبة.", checks: [{ result: "يكتب Good 100% واللون أزرق", verdict: "safe", msg: "القرص بصحة ممتازة" }, { result: "يكتب Warning / Bad أو اللون أحمر", verdict: "danger", msg: "القرص يحتضر وسيضيع بياناتك." }] }
        ]
    };

    // قاعدة بيانات الأعطال والحلول
    const fixesDB = [
        { issue: "الهاتف عالق في شعار الشركة (Bootloop)", type: "Software", fix: "أدخل لوضع الريكفري (زر الطاقة + رفع الصوت)، اختر Wipe Cache. إذا لم ينجح، يجب تفليش (ROM) رسمي عبر Odin لسامسونج أو 3uTools لآيفون." },
        { issue: "الكمبيوتر يظهر شاشة زرقاء (BSOD)", type: "Hardware/Software", fix: "افحص كود الخطأ. إذا كان 'MEMORY_MANAGEMENT' المشكل في الرام (قم بتنظيف أسنانها النحاسية). إذا كان المشكل بعد تحديث، ادخل Safe Mode واحذف آخر درايفر (GPU)." },
        { issue: "الهاتف يرفض الشحن تماماً", type: "Hardware", fix: "قم بتنظيف منفذ الشحن بإبرة خشبية (تراكم الغبار). إذا لم ينجح، المشكل في (Charging IC) أو الـ Sub-board ويحتاج استبدال عند تقني Micro-soldering." }
    ];

    // قاعدة بيانات العباقرة والسوق المغربي
    const loreDB = {
        hackers: [
            { name: "George Hotz (Geohot)", role: "أسطورة اختراق الهاردوير", desc: "اخترق Baseband الآيفون في سن 17، واستخرج مفاتيح PlayStation 3." },
            { name: "Joe Grand (Kingpin)", role: "إمبراطور شرائح eMMC", desc: "يخترق الذاكرة فيزيائياً عبر التلحيم الدقيق (ISP) لمسح حمايات FRP من الجذور." },
            { name: "John Wu (topjohnwu)", role: "مبتكر Magisk", desc: "اخترع الروت الخفي (Systemless) وتلاعب بـ Boot.img ليخدع حمايات جوجل، حتى اضطرت جوجل لتوظيفه!" }
        ],
        engineers: [
            { name: "Ivan Krstić", role: "رئيس هندسة أمن آبل", desc: "مصمم الغرفة المحصنة (Secure Enclave) التي تحفظ البصمة ومفاتيح التشفير." },
            { name: "Sophie Wilson", role: "مؤسسة معمارية ARM", desc: "صممت أساس معالجات 99% من هواتف العالم." }
        ],
        morocco: [
            { title: "طريق الميناء (السوق الرسمي)", desc: "شركات مثل Disway و Disty تستورد الحواسيب بضمان الوكالة، تدفع ضريبة TVA 20% وجمارك، وتباع في مرجان وإلكتروبلانيت." },
            { title: "طريق دبي / كراج علال (السوق الموازي)", desc: "استيراد ضخم لتجار الجملة (هواتف جديدة بدون ضمان رسمي محلي). الأسعار تكون أرخص بـ 20% لغياب الضرائب المباشرة، ويباع في درب غلف." },
            { title: "سوق الكابا (المستعمل)", desc: "حواسيب للشركات الأوروبية (Leasing) تدخل عبر الحافلات من إسبانيا وفرنسا. ممتازة للاستعمال المتوسط ولكن تتطلب فحصاً دقيقاً للبطارية والقرص." }
        ]
    };

    // ==========================================
    // 2. المكونات الرئيسية للتطبيق (Components)
    // ==========================================

    const App = () => {
        const [activeTab, setActiveTab] = useState('home');

        const TabButton = ({ id, icon, label }) => (
            <button onClick={() => setActiveTab(id)} className={`flex flex-col items-center justify-center w-full py-3 transition-colors ${activeTab === id ? 'text-brand-primary border-t-2 border-brand-primary bg-dark-800' : 'text-gray-500 hover:text-gray-300'}`}>
                <i className={`fa-solid ${icon} text-xl mb-1`}></i>
                <span className="text-[10px] font-bold">{label}</span>
            </button>
        );

        return (
            <div className="flex flex-col min-h-screen bg-dark-900">
                {/* Header */}
                <header className="glass-panel sticky top-0 z-50 px-6 py-4 flex justify-between items-center shadow-lg">
                    <div className="flex items-center gap-2">
                        <div className="bg-gradient-to-r from-brand-primary to-brand-secondary p-2 rounded-lg"><i className="fa-solid fa-microchip text-white"></i></div>
                        <h1 className="text-xl font-black text-white">Aura<span className="text-brand-primary">Tech</span> AI</h1>
                    </div>
                    <div className="hidden md:flex gap-4">
                        {/* Desktop Nav */}
                        <button onClick={() => setActiveTab('recommender')} className="text-sm font-bold hover:text-brand-primary transition">مستشار الشراء</button>
                        <button onClick={() => setActiveTab('inspector')} className="text-sm font-bold hover:text-brand-primary transition">المحقق الميداني</button>
                        <button onClick={() => setActiveTab('stores')} className="text-sm font-bold hover:text-brand-primary transition">الأسواق والأسعار</button>
                    </div>
                </header>

                {/* Main Content Area */}
                <main className="flex-1 w-full max-w-5xl mx-auto p-4 pb-24 md:pb-6">
                    {activeTab === 'home' && <HomeView setTab={setActiveTab} />}
                    {activeTab === 'recommender' && <RecommenderView />}
                    {activeTab === 'inspector' && <InspectorView />}
                    {activeTab === 'troubleshoot' && <TroubleshootView />}
                    {activeTab === 'stores' && <StoresPricesView />}
                    {activeTab === 'lore' && <LoreSecretsView />}
                </main>

                {/* Mobile Bottom Navigation */}
                <nav className="md:hidden glass-panel fixed bottom-0 w-full flex justify-between border-t border-dark-700 pb-safe z-50">
                    <TabButton id="home" icon="fa-house" label="الرئيسية" />
                    <TabButton id="recommender" icon="fa-robot" label="مستشار" />
                    <TabButton id="inspector" icon="fa-magnifying-glass-chart" label="محقق" />
                    <TabButton id="stores" icon="fa-store" label="أسواق" />
                    <TabButton id="troubleshoot" icon="fa-wrench" label="إصلاح" />
                    <TabButton id="lore" icon="fa-book-skull" label="كواليس" />
                </nav>
            </div>
        );
    };

    // --- واجهة الرئيسية ---
    const HomeView = ({ setTab }) => (
        <div className="animate-fade-in space-y-6 mt-4">
            <div className="bg-gradient-to-bl from-dark-800 to-dark-900 border border-dark-700 rounded-3xl p-8 text-center shadow-2xl relative overflow-hidden">
                <div className="absolute -top-10 -right-10 w-40 h-40 bg-brand-primary/20 blur-3xl rounded-full"></div>
                <h2 className="text-3xl font-black text-white mb-4 z-10 relative">منصتك لاختراق عالم التكنولوجيا</h2>
                <p className="text-gray-400 text-sm max-w-md mx-auto leading-relaxed z-10 relative">لا مزيد من النصب في الأسواق. ابحث عن جهازك، افحصه بنفسك، قارن أسعاره الحقيقية، واكتشف أسرار الهاكرز والأسواق المغربية.</p>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                <DashboardCard icon="fa-robot" color="text-brand-primary" bg="bg-brand-primary/10" title="مستشار الشراء" desc="شنو نشري؟" onClick={() => setTab('recommender')} />
                <DashboardCard icon="fa-magnifying-glass-chart" color="text-yellow-500" bg="bg-yellow-500/10" title="المحقق الميداني" desc="فحص القطع" onClick={() => setTab('inspector')} />
                <DashboardCard icon="fa-store" color="text-green-500" bg="bg-green-500/10" title="دليل الأسواق" desc="المتاجر والأسعار" onClick={() => setTab('stores')} />
                <DashboardCard icon="fa-wrench" color="text-red-500" bg="bg-red-500/10" title="العيادة التقنية" desc="حلول ومشاكل" onClick={() => setTab('troubleshoot')} />
                <DashboardCard icon="fa-book-skull" color="text-purple-500" bg="bg-purple-500/10" title="الموسوعة" desc="هاكرز وكواليس" onClick={() => setTab('lore')} />
            </div>
        </div>
    );

    const DashboardCard = ({ icon, color, bg, title, desc, onClick }) => (
        <div onClick={onClick} className="glass-panel p-6 rounded-2xl cursor-pointer hover:border-brand-primary transition-all group">
            <div className={`w-12 h-12 ${bg} ${color} rounded-xl flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform`}><i className={`fa-solid ${icon}`}></i></div>
            <h3 className="font-bold text-white mb-1">{title}</h3>
            <p className="text-xs text-gray-500">{desc}</p>
        </div>
    );

    // --- مستشار الشراء الذكي ---
    const RecommenderView = () => {
        const [step, setStep] = useState(0);
        const [type, setType] = useState('');
        const [usage, setUsage] = useState('');

        const getResult = () => {
            if (type === 'pc') {
                if (usage === 'heavy') return { name: "Lenovo Legion Pro 7i", specs: "i9-14th, RTX 4080, 32GB RAM", price: "25,000 DH" };
                return { name: "MacBook Air M2 / Lenovo ThinkPad", specs: "16GB RAM, 512GB SSD", price: "12,000 DH" };
            } else {
                if (usage === 'heavy') return { name: "iPhone 15 Pro Max / S24 Ultra", specs: "A17 Pro / Snapdragon 8 Gen 3", price: "13,000 DH" };
                return { name: "Samsung Galaxy A55 / Poco X6 Pro", specs: "Exynos 1480 / Dimensity 8300", price: "4,000 DH" };
            }
        };

        return (
            <div className="animate-slide-up space-y-6">
                <h2 className="text-2xl font-black text-white"><i className="fa-solid fa-robot text-brand-primary"></i> مستشار الشراء الذكي</h2>
                
                {step === 0 && (
                    <div className="grid gap-4">
                        <h3 className="text-gray-400">شنو باغي تشري؟</h3>
                        <button onClick={() => {setType('pc'); setStep(1);}} className="glass-panel p-6 rounded-2xl text-right hover:border-brand-primary"><h4 className="font-bold text-xl"><i className="fa-solid fa-laptop text-brand-primary ml-2"></i>حاسوب (PC/Mac)</h4></button>
                        <button onClick={() => {setType('phone'); setStep(1);}} className="glass-panel p-6 rounded-2xl text-right hover:border-brand-primary"><h4 className="font-bold text-xl"><i className="fa-solid fa-mobile text-brand-secondary ml-2"></i>هاتف ذكي</h4></button>
                    </div>
                )}
                
                {step === 1 && (
                    <div className="grid gap-4 animate-fade-in">
                        <h3 className="text-gray-400">شنو غدير بيه بالضبط؟ (البرامج والتطبيقات)</h3>
                        <button onClick={() => {setUsage('heavy'); setStep(2);}} className="glass-panel p-4 rounded-xl text-right hover:border-brand-primary"><strong>استعمال ثقيل:</strong> ألعاب قوية، مونتاج (Premiere/Lumion)، 3D.</button>
                        <button onClick={() => {setUsage('light'); setStep(2);}} className="glass-panel p-4 rounded-xl text-right hover:border-brand-primary"><strong>استعمال عادي:</strong> تصفح، أفلام، Word/Excel، سوشيال ميديا.</button>
                    </div>
                )}

                {step === 2 && (
                    <div className="glass-panel p-8 rounded-3xl text-center border-brand-primary animate-fade-in shadow-[0_0_30px_rgba(59,130,246,0.2)]">
                        <i className="fa-solid fa-bullseye text-5xl text-brand-primary mb-4"></i>
                        <h3 className="text-xl font-bold text-white mb-2">هذا هو الجهاز المناسب لمتطلباتك:</h3>
                        <div className="bg-dark-800 p-4 rounded-xl inline-block mt-4 text-right">
                            <h4 className="text-2xl font-black text-brand-secondary mb-2">{getResult().name}</h4>
                            <p className="text-sm text-gray-400"><i className="fa-solid fa-microchip ml-2"></i>{getResult().specs}</p>
                            <p className="text-sm text-gray-400 mt-2"><i className="fa-solid fa-tag ml-2"></i>السعر التقريبي: <strong className="text-white">{getResult().price}</strong></p>
                        </div>
                        <button onClick={() => setStep(0)} className="w-full mt-6 bg-dark-700 py-3 rounded-xl font-bold hover:bg-dark-600">بحث جديد</button>
                    </div>
                )}
            </div>
        );
    };

    // --- المحقق الميداني ---
    const InspectorView = () => {
        const [device, setDevice] = useState(null);
        const [step, setStep] = useState(0);
        const [report, setReport] = useState([]);

        const handleAnswer = (verdict, msg) => {
            setReport([...report, { step: inspectionDB[device][step].step, verdict, msg }]);
            if (step < inspectionDB[device].length - 1) setStep(step + 1);
            else setStep(99);
        };

        if (!device) return (
            <div className="animate-slide-up space-y-6">
                <h2 className="text-2xl font-black text-white"><i className="fa-solid fa-magnifying-glass-chart text-yellow-500"></i> المحقق الميداني</h2>
                <p className="text-gray-400">أنت الآن عند البائع؟ اختر الجهاز لنبدأ الفحص المباشر للقطع.</p>
                <div className="grid grid-cols-2 gap-4">
                    <button onClick={() => setDevice('phone')} className="glass-panel p-6 rounded-2xl hover:border-yellow-500"><i className="fa-solid fa-mobile text-4xl text-yellow-500 mb-2"></i><h4 className="font-bold">فحص هاتف</h4></button>
                    <button onClick={() => setDevice('pc')} className="glass-panel p-6 rounded-2xl hover:border-yellow-500"><i className="fa-solid fa-laptop text-4xl text-blue-500 mb-2"></i><h4 className="font-bold">فحص حاسوب</h4></button>
                </div>
            </div>
        );

        if (step === 99) {
            const hasDanger = report.some(r => r.verdict === 'danger');
            return (
                <div className="animate-fade-in space-y-4">
                    <div className={`p-6 rounded-2xl text-center border ${hasDanger ? 'bg-red-900/20 border-red-500' : 'bg-green-900/20 border-green-500'}`}>
                        <i className={`fa-solid ${hasDanger ? 'fa-triangle-exclamation text-red-500' : 'fa-shield-check text-green-500'} text-6xl mb-4`}></i>
                        <h3 className="text-2xl font-black mb-2">{hasDanger ? 'احذر! الجهاز به مشاكل' : 'ممتاز! الجهاز سليم'}</h3>
                    </div>
                    <div className="space-y-3">
                        {report.map((r, i) => (
                            <div key={i} className={`glass-panel p-4 rounded-xl border-r-4 ${r.verdict === 'danger' ? 'border-red-500' : 'border-green-500'}`}>
                                <h4 className="font-bold text-sm text-gray-300">{r.step}</h4>
                                <p className={`text-xs mt-1 font-bold ${r.verdict === 'danger' ? 'text-red-400' : 'text-green-400'}`}>{r.msg}</p>
                            </div>
                        ))}
                    </div>
                    <button onClick={() => {setDevice(null); setStep(0); setReport([]);}} className="w-full bg-dark-700 py-4 rounded-xl font-bold mt-4">فحص جهاز آخر</button>
                </div>
            );
        }

        const currentQ = inspectionDB[device][step];
        return (
            <div className="animate-slide-up space-y-6">
                <div className="flex justify-between items-center text-sm font-bold text-gray-500 border-b border-dark-700 pb-2">
                    <span>خطوة {step + 1} من {inspectionDB[device].length}</span>
                    <span className="bg-yellow-500/20 text-yellow-500 px-2 py-1 rounded">قيد الفحص</span>
                </div>
                <div className="glass-panel p-6 rounded-2xl border-yellow-500/30">
                    <h3 className="text-xl font-bold text-white mb-4">{currentQ.step}</h3>
                    <p className="bg-dark-800 p-4 rounded-xl text-yellow-400 text-sm border border-dark-700"><i className="fa-solid fa-terminal mr-2"></i>{currentQ.action}</p>
                </div>
                <h4 className="font-bold text-gray-400 text-center">ماذا ظهر لك الآن؟</h4>
                <div className="grid gap-3">
                    {currentQ.checks.map((check, i) => (
                        <button key={i} onClick={() => handleAnswer(check.verdict, check.msg)} className="glass-panel p-4 rounded-xl text-right hover:bg-dark-700 transition flex justify-between items-center">
                            <span className="text-sm font-bold">{check.result}</span>
                            <i className="fa-solid fa-chevron-left text-dark-500"></i>
                        </button>
                    ))}
                </div>
            </div>
        );
    };

    // --- دليل الأسواق والأسعار ---
    const StoresPricesView = () => (
        <div className="animate-slide-up space-y-8">
            <h2 className="text-2xl font-black text-white"><i className="fa-solid fa-store text-green-500"></i> خريطة المتاجر والأسعار الحقيقية</h2>
            
            {/* جدول الأسعار */}
            <div className="glass-panel rounded-2xl overflow-hidden">
                <div className="bg-dark-800 p-4 border-b border-dark-700"><h3 className="font-bold">مقارنة الأسعار (السوق الرسمي vs الموازي)</h3></div>
                <div className="overflow-x-auto">
                    <table className="w-full text-right text-sm">
                        <thead className="bg-dark-900 text-gray-500">
                            <tr><th className="p-4">الجهاز</th><th className="p-4">الوكالة (رسمي)</th><th className="p-4">درب غلف (Grey)</th><th className="p-4">الجملة</th></tr>
                        </thead>
                        <tbody>
                            {pricesDB.map((p, i) => (
                                <tr key={i} className="border-b border-dark-800 last:border-0 hover:bg-dark-800/50 transition">
                                    <td className="p-4 font-bold text-white">{p.device}</td>
                                    <td className="p-4 text-red-400">{p.official}</td>
                                    <td className="p-4 text-green-400">{p.grey}</td>
                                    <td className="p-4 text-yellow-400">{p.wholesale}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* دليل المتاجر */}
            <h3 className="font-bold text-xl text-white mt-8 mb-4">دليل أبرز المتاجر والموردين</h3>
            <div className="grid md:grid-cols-2 gap-4">
                {storesDB.map(store => (
                    <div key={store.id} className="glass-panel p-5 rounded-2xl border border-dark-700 relative overflow-hidden">
                        <div className={`absolute top-0 right-0 px-3 py-1 text-[10px] font-bold rounded-bl-lg ${store.type.includes('رسمي') ? 'bg-blue-600' : 'bg-green-600'}`}>{store.type}</div>
                        <h4 className="font-black text-lg text-white mb-1 mt-2">{store.name}</h4>
                        <p className="text-xs text-gray-400 mb-4">{store.desc}</p>
                        <div className="space-y-2 text-sm text-gray-300">
                            <p><i className="fa-solid fa-phone w-5 text-gray-500"></i> {store.phone}</p>
                            <p><i className="fa-solid fa-envelope w-5 text-gray-500"></i> {store.email}</p>
                            <p><i className="fa-brands fa-instagram w-5 text-purple-500"></i> {store.social}</p>
                        </div>
                        <a href={store.map} target="_blank" className="mt-4 block w-full bg-dark-800 text-center py-2 rounded-lg text-sm font-bold hover:bg-dark-700 transition"><i className="fa-solid fa-map-location-dot text-green-500 ml-2"></i>افتح في خريطة جوجل</a>
                    </div>
                ))}
            </div>
        </div>
    );

    // --- العيادة التقنية ---
    const TroubleshootView = () => (
        <div className="animate-slide-up space-y-6">
            <h2 className="text-2xl font-black text-white"><i className="fa-solid fa-wrench text-red-500"></i> العيادة التقنية (أعطال وحلول)</h2>
            <p className="text-gray-400 text-sm mb-6">الحلول التقنية لأشهر مشاكل الهاردوير والسوفتوير التي يواجهها المستخدم يومياً.</p>
            <div className="space-y-4">
                {fixesDB.map((fix, i) => (
                    <div key={i} className="glass-panel p-5 rounded-2xl border-l-4 border-red-500">
                        <div className="flex justify-between items-start mb-2">
                            <h3 className="font-bold text-white text-lg">{fix.issue}</h3>
                            <span className="bg-dark-800 text-xs px-2 py-1 rounded text-gray-400 border border-dark-700">{fix.type}</span>
                        </div>
                        <p className="text-sm text-green-400 bg-green-900/10 p-3 rounded-lg border border-green-900/30 mt-3 leading-relaxed"><i className="fa-solid fa-check-circle mr-2"></i>{fix.fix}</p>
                    </div>
                ))}
            </div>
        </div>
    );

    // --- موسوعة كواليس التقنية ---
    const LoreSecretsView = () => (
        <div className="animate-slide-up space-y-8">
            <h2 className="text-2xl font-black text-white"><i className="fa-solid fa-book-skull text-purple-500"></i> كواليس وأباطرة التقنية</h2>
            
            <section>
                <h3 className="text-xl font-bold text-white mb-4 border-b border-dark-700 pb-2"><i className="fa-solid fa-user-ninja text-purple-400 ml-2"></i>أباطرة الاختراق (Hackers)</h3>
                <div className="grid gap-3">
                    {loreDB.hackers.map((h, i) => (
                        <div key={i} className="glass-panel p-4 rounded-xl border border-purple-500/30">
                            <h4 className="font-black text-purple-400">{h.name} <span className="text-xs text-gray-500 font-normal bg-dark-800 px-2 py-0.5 rounded ml-2">{h.role}</span></h4>
                            <p className="text-sm text-gray-300 mt-2">{h.desc}</p>
                        </div>
                    ))}
                </div>
            </section>

            <section>
                <h3 className="text-xl font-bold text-white mb-4 border-b border-dark-700 pb-2"><i className="fa-solid fa-hard-hat text-blue-400 ml-2"></i>أباطرة الهندسة (Engineers)</h3>
                <div className="grid gap-3">
                    {loreDB.engineers.map((e, i) => (
                        <div key={i} className="glass-panel p-4 rounded-xl border border-blue-500/30">
                            <h4 className="font-black text-blue-400">{e.name} <span className="text-xs text-gray-500 font-normal bg-dark-800 px-2 py-0.5 rounded ml-2">{e.role}</span></h4>
                            <p className="text-sm text-gray-300 mt-2">{e.desc}</p>
                        </div>
                    ))}
                </div>
            </section>

            <section>
                <h3 className="text-xl font-bold text-white mb-4 border-b border-dark-700 pb-2"><i className="fa-solid fa-flag-ma text-red-500 ml-2"></i>كواليس السوق المغربي</h3>
                <div className="grid gap-3">
                    {loreDB.morocco.map((m, i) => (
                        <div key={i} className="glass-panel p-5 rounded-xl border-r-4 border-red-600 bg-gradient-to-l from-red-900/10 to-transparent">
                            <h4 className="font-bold text-white">{m.title}</h4>
                            <p className="text-sm text-gray-400 mt-2 leading-relaxed">{m.desc}</p>
                        </div>
                    ))}
                </div>
            </section>
        </div>
    );

    const root = ReactDOM.createRoot(document.getElementById('root'));
    root.render(<App />);
</script>
</body>
</html>
