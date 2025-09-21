# 🚚 Netaj Dashboard - Transportation Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge&logo=php&logoColor=white" alt="Filament">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

## 📋 Overview

A comprehensive transportation and commercial document management system built with Laravel and Filament Admin Panel. The system provides integrated management for all transportation, supply, and invoicing operations.

## ✨ Key Features

### 📄 Document Management
- **Receipt Documents** - Manage goods receipt documents
- **Delivery Documents** - Track goods delivery operations
- **Sales Invoices** - Create and manage sales invoices
- **Purchase Invoices** - Track company purchases

### 👥 Party Management
- **Customers** - Comprehensive customer database
- **Suppliers** - Manage supplier information
- **Transporters** - Track contracted transportation companies

### 📦 Product Management
- Comprehensive product catalog
- Price and unit tracking
- Inventory management

### 🔍 Advanced Filtering System
- Collapsible and customizable filters
- Advanced search across all data
- Multi-format data export

### 🖊️ Digital Signatures
- Digital signatures for officials
- Save and export signatures
- Document security

## 🛠️ Technologies Used

- **Backend**: Laravel 11.x
- **Admin Panel**: Filament 3.x
- **Database**: MySQL
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **Export**: Filament Export
- **Signatures**: Filament Autograph

## 📦 Installation

### Requirements
- PHP 8.2 or newer
- Composer
- Node.js & NPM
- MySQL 8.0 or newer

### Installation Steps

1. **Clone the project**
```bash
git clone https://github.com/your-username/netaj-dashboard.git
cd netaj-dashboard
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**
```bash
# Update database information in .env file
php artisan migrate
php artisan db:seed
```

5. **Build assets**
```bash
npm run build
```

6. **Run server**
```bash
php artisan serve
```

## 🚀 Usage

### System Access
- Open browser at: `http://localhost:8000`
- Login with default admin credentials

### Main Modules

#### 📋 Document Management
- Navigate to "Document Management" section
- Choose required document type
- Create or edit documents

#### 👤 User Management
- "User Management" section for user administration
- Define roles and permissions
- Manage user profiles

#### 📊 Reports and Export
- Use export buttons in each table
- Choose required format (Excel, CSV, PDF)
- Customize exported data

## 🔧 Customization

### Adding New Fields
1. Create new migration
2. Update corresponding model
3. Add field in Filament Resource

### Interface Customization
- Files in `app/Filament/Resources`
- Color customization in `config/filament.php`
- Add custom CSS in `resources/css`

## 📝 Project Structure

```
app/
├── Filament/
│   ├── Resources/          # Filament Resources
│   └── Widgets/           # Widgets
├── Models/                # Data Models
├── Policies/              # Permission Policies
└── Providers/             # Service Providers

database/
├── migrations/            # Migration Files
└── seeders/              # Data Seeders

resources/
├── css/                  # Style Files
├── js/                   # JavaScript Files
└── views/                # Templates
```

## 🤝 Contributing

We welcome your contributions! Please follow these steps:

1. Fork the project
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see [LICENSE](LICENSE) file for details.

## 📞 Contact

- **Developer**: [Your Name]
- **Email**: [your-email@example.com]
- **GitHub**: [https://github.com/your-username](https://github.com/your-username)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - Core framework
- [Filament](https://filamentphp.com) - Admin panel
- [Spatie](https://spatie.be) - Useful Laravel packages

---

<p align="center">
  Made with ❤️ for better transportation and commercial document management
</p>

---

# 🚚 نظام إدارة النقليات - Netaj Dashboard

## 📋 نظرة عامة

نظام إدارة شامل للنقليات والمستندات التجارية مبني بتقنية Laravel و Filament Admin Panel. يوفر النظام إدارة متكاملة لجميع عمليات النقل والتوريد والفواتير.

## ✨ الميزات الرئيسية

### 📄 إدارة المستندات
- **سندات الاستلام** - إدارة مستندات استلام البضائع
- **سندات التسليم** - تتبع عمليات تسليم البضائع
- **فواتير المبيعات** - إنشاء وإدارة فواتير البيع
- **فواتير المشتريات** - تتبع مشتريات الشركة

### 👥 إدارة الأطراف
- **العملاء** - قاعدة بيانات شاملة للعملاء
- **الموردين** - إدارة معلومات الموردين
- **شركات النقل** - تتبع شركات النقل المتعاقد معها

### 📦 إدارة المنتجات
- كتالوج شامل للمنتجات
- تتبع الأسعار والوحدات
- إدارة المخزون

### 🔍 نظام الفلترة المتقدم
- فلاتر قابلة للطي والتخصيص
- بحث متقدم في جميع البيانات
- تصدير البيانات بصيغ متعددة

### 🖊️ التوقيعات الرقمية
- توقيعات رقمية للمسؤولين
- حفظ وتصدير التوقيعات
- تأمين المستندات

## 🛠️ التقنيات المستخدمة

- **Backend**: Laravel 11.x
- **Admin Panel**: Filament 3.x
- **Database**: MySQL
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **Export**: Filament Export
- **Signatures**: Filament Autograph

## 📦 التثبيت

### المتطلبات
- PHP 8.2 أو أحدث
- Composer
- Node.js & NPM
- MySQL 8.0 أو أحدث

### خطوات التثبيت

1. **استنساخ المشروع**
```bash
git clone https://github.com/your-username/netaj-dashboard.git
cd netaj-dashboard
```

2. **تثبيت التبعيات**
```bash
composer install
npm install
```

3. **إعداد البيئة**
```bash
cp .env.example .env
php artisan key:generate
```

4. **إعداد قاعدة البيانات**
```bash
# قم بتحديث معلومات قاعدة البيانات في ملف .env
php artisan migrate
php artisan db:seed
```

5. **بناء الأصول**
```bash
npm run build
```

6. **تشغيل الخادم**
```bash
php artisan serve
```

## 🚀 الاستخدام

### الوصول للنظام
- افتح المتصفح على: `http://localhost:8000`
- قم بتسجيل الدخول باستخدام بيانات المدير الافتراضية

### الوحدات الرئيسية

#### 📋 إدارة المستندات
- انتقل إلى قسم "Document Management"
- اختر نوع المستند المطلوب
- قم بإنشاء أو تعديل المستندات

#### 👤 إدارة المستخدمين
- قسم "User Management" لإدارة المستخدمين
- تحديد الأدوار والصلاحيات
- إدارة ملفات المستخدمين

#### 📊 التقارير والتصدير
- استخدم أزرار التصدير في كل جدول
- اختر الصيغة المطلوبة (Excel, CSV, PDF)
- قم بتخصيص البيانات المصدرة

## 🔧 التخصيص

### إضافة حقول جديدة
1. قم بإنشاء migration جديد
2. حدث النموذج المقابل
3. أضف الحقل في Filament Resource

### تخصيص الواجهة
- الملفات في `app/Filament/Resources`
- تخصيص الألوان في `config/filament.php`
- إضافة CSS مخصص في `resources/css`

## 📝 هيكل المشروع

```
app/
├── Filament/
│   ├── Resources/          # موارد Filament
│   └── Widgets/           # الودجات
├── Models/                # نماذج البيانات
├── Policies/              # سياسات الصلاحيات
└── Providers/             # مقدمي الخدمات

database/
├── migrations/            # ملفات الهجرة
└── seeders/              # بذور البيانات

resources/
├── css/                  # ملفات الأنماط
├── js/                   # ملفات JavaScript
└── views/                # القوالب
```

## 🤝 المساهمة

نرحب بمساهماتكم! يرجى اتباع الخطوات التالية:

1. Fork المشروع
2. إنشاء فرع للميزة الجديدة (`git checkout -b feature/AmazingFeature`)
3. Commit التغييرات (`git commit -m 'Add some AmazingFeature'`)
4. Push للفرع (`git push origin feature/AmazingFeature`)
5. فتح Pull Request

## 📄 الترخيص

هذا المشروع مرخص تحت رخصة MIT - راجع ملف [LICENSE](LICENSE) للتفاصيل.

## 📞 التواصل

- **المطور**: [اسمك]
- **البريد الإلكتروني**: [your-email@example.com]
- **GitHub**: [https://github.com/your-username](https://github.com/your-username)

## 🙏 شكر وتقدير

- [Laravel](https://laravel.com) - إطار العمل الأساسي
- [Filament](https://filamentphp.com) - لوحة الإدارة
- [Spatie](https://spatie.be) - حزم Laravel المفيدة

---

<p align="center">
  صُنع بـ ❤️ لإدارة أفضل للنقليات والمستندات التجارية
</p>
