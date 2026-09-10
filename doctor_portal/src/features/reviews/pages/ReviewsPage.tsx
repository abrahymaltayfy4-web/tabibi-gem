import React from 'react';
import { Star, ShieldCheck } from 'lucide-react';

export const ReviewsPage: React.FC = () => {
  const reviews = [
    {
      id: 1,
      patientName: 'محمد عبدالله باوزير',
      rating: 5,
      date: '09 سبتمبر 2026',
      comment: 'دكتور رائع جداً ومستمع ممتاز. قام بتشخيص الحالة بدقة وتوضيح خطة العلاج بأسلوب مبسط وراقي.',
      verifiedConsultation: true,
    },
    {
      id: 2,
      patientName: 'فاطمة أحمد سالم',
      rating: 5,
      date: '05 سبتمبر 2026',
      comment: 'شكراً جزيلاً دكتور أحمد على التجاوب السريع والتوضيح الكافي للنتائج.',
      verifiedConsultation: true,
    },
    {
      id: 3,
      patientName: 'عمر خالد العمودي',
      rating: 4,
      date: '28 أغسطس 2026',
      comment: 'استشارة ملمة بالكامل والتزام تام بموعد الجلسة المرئية.',
      verifiedConsultation: true,
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200/80 pb-4 dark:border-slate-800">
        <div>
          <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">تقييمات وآراء المرضى</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">استعراض ملاحظات وتقييمات المرضى الصادرة بعد إتمام الاستشارات الناجحة</p>
        </div>
      </div>

      {/* Aggregate Rating Banner */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div className="rounded-2xl border border-slate-200/80 bg-white p-6 text-center shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-2">
          <span className="text-4xl font-black text-slate-900 dark:text-slate-100">4.9</span>
          <div className="flex items-center justify-center gap-1 text-amber-400">
            {[...Array(5)].map((_, i) => (
              <Star key={i} className="h-5 w-5 fill-amber-400" />
            ))}
          </div>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">متوسط التقييم العام (من 128 تقييم)</p>
        </div>

        <div className="sm:col-span-2 rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-2">
          <h2 className="text-xs font-bold text-slate-700 dark:text-slate-300">توزيع التقييمات الإجمالي</h2>
          {[
            { stars: 5, pct: '94%' },
            { stars: 4, pct: '5%' },
            { stars: 3, pct: '1%' },
          ].map((item) => (
            <div key={item.stars} className="flex items-center gap-3 text-xs">
              <span className="w-12 font-bold text-slate-600 dark:text-slate-400">{item.stars} نجوم</span>
              <div className="flex-1 h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                <div className="h-full bg-amber-400 rounded-full" style={{ width: item.pct }}></div>
              </div>
              <span className="w-10 font-mono font-bold text-slate-500">{item.pct}</span>
            </div>
          ))}
        </div>
      </div>

      {/* Patient Comments List */}
      <div className="space-y-4">
        {reviews.map((rev) => (
          <div key={rev.id} className="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-900 space-y-3">
            <div className="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
              <div className="flex items-center gap-3">
                <div className="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-xs">
                  {rev.patientName.charAt(0)}
                </div>
                <div>
                  <h3 className="text-xs font-bold text-slate-900 dark:text-slate-100">{rev.patientName}</h3>
                  <p className="text-[10px] text-slate-400">{rev.date}</p>
                </div>
              </div>

              <div className="flex items-center gap-1 text-amber-400">
                {[...Array(rev.rating)].map((_, i) => (
                  <Star key={i} className="h-3.5 w-3.5 fill-amber-400" />
                ))}
              </div>
            </div>

            <p className="text-xs font-medium text-slate-700 dark:text-slate-300 leading-relaxed">{rev.comment}</p>

            {rev.verifiedConsultation && (
              <span className="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                <ShieldCheck className="h-3.5 w-3.5" />
                استشارة مؤكدة ومكتملة على منصة طبيبي
              </span>
            )}
          </div>
        ))}
      </div>
    </div>
  );
};
