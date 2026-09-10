import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Stethoscope, UploadCloud, CheckCircle2, ShieldAlert, ArrowLeft, ArrowRight, FileText, Banknote, Award } from 'lucide-react';
import { useAuthStore } from '../../../core/auth/useAuthStore';

export const OnboardingPage: React.FC = () => {
  const [currentStep, setCurrentStep] = useState<1 | 2 | 3>(1);
  const { doctorProfile, setDoctorProfile } = useAuthStore();
  const navigate = useNavigate();

  const [formData, setFormData] = useState({
    qualificationAr: doctorProfile?.qualificationAr || '',
    qualificationEn: doctorProfile?.qualificationEn || '',
    experienceYears: doctorProfile?.experienceYears || 5,
    consultationFeeYer: doctorProfile?.consultationFeeYer || 15000,
    consultationDurationMinutes: doctorProfile?.consultationDurationMinutes || 30,
    bioAr: '',
  });

  const [uploadedFiles, setUploadedFiles] = useState<{ [key: string]: string }>({});

  const handleFileUpload = (docType: string, e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (file) {
      setUploadedFiles((prev) => ({ ...prev, [docType]: file.name }));
    }
  };

  const handleFinishOnboarding = () => {
    if (doctorProfile) {
      setDoctorProfile({
        ...doctorProfile,
        qualificationAr: formData.qualificationAr,
        qualificationEn: formData.qualificationEn,
        experienceYears: Number(formData.experienceYears),
        consultationFeeYer: Number(formData.consultationFeeYer),
        consultationDurationMinutes: Number(formData.consultationDurationMinutes),
        verificationStatus: 'pending', // Submitted for admin verification
      });
    }
    navigate('/verification-status');
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-slate-50 to-blue-50/50 p-4 dark:from-[#0C162A] dark:to-[#0C1A37]">
      <div className="w-full max-w-2xl space-y-6 rounded-3xl border border-slate-200/80 bg-white/95 p-8 shadow-2xl backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/95">
        {/* Header */}
        <div className="text-center space-y-2">
          <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#29508B] to-[#1E3D6B] text-white shadow-lg shadow-[#29508B]/30">
            <Stethoscope className="h-7 w-7" />
          </div>
          <h1 className="text-2xl font-black text-slate-900 dark:text-slate-100">معالج إكمال البيانات والتوثيق المعتمد</h1>
          <p className="text-xs font-semibold text-slate-500 dark:text-slate-400">يرجى استكمال البيانات وإرفاق الوثائق لإرسال طلبك لإدارة منصة طبيبي</p>
        </div>

        {/* Step Indicator */}
        <div className="flex items-center justify-between border-b border-slate-100 pb-4 dark:border-slate-800">
          {[
            { step: 1, label: 'المؤهلات والخبرة', icon: Award },
            { step: 2, label: 'الرسوم وزمن الجلسة', icon: Banknote },
            { step: 3, label: 'رفع الوثائق والتراخيص', icon: FileText },
          ].map((item) => (
            <div
              key={item.step}
              className={`flex items-center gap-2 text-xs font-bold ${
                currentStep === item.step
                  ? 'text-[#29508B] dark:text-blue-400'
                  : currentStep > item.step
                  ? 'text-emerald-600 dark:text-emerald-400'
                  : 'text-slate-400'
              }`}
            >
              <div
                className={`flex h-8 w-8 items-center justify-center rounded-full text-xs font-black ${
                  currentStep === item.step
                    ? 'bg-[#29508B] text-white shadow-md shadow-[#29508B]/20'
                    : currentStep > item.step
                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'
                    : 'bg-slate-100 text-slate-500 dark:bg-slate-800'
                }`}
              >
                {currentStep > item.step ? <CheckCircle2 className="h-4 w-4" /> : item.step}
              </div>
              <span className="hidden sm:inline">{item.label}</span>
            </div>
          ))}
        </div>

        {/* Wizard Steps */}
        {currentStep === 1 && (
          <div className="space-y-4">
            <div className="space-y-1.5">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">المؤهل العلمي والدكتوراة (بالعربية)</label>
              <input
                type="text"
                value={formData.qualificationAr}
                onChange={(e) => setFormData({ ...formData, qualificationAr: e.target.value })}
                placeholder="دكتوراه في الطب الباطني والجهاز الهضمي"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
            </div>

            <div className="space-y-1.5">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">المؤهل العلمي (بالإنجليزية)</label>
              <input
                type="text"
                value={formData.qualificationEn}
                onChange={(e) => setFormData({ ...formData, qualificationEn: e.target.value })}
                placeholder="PhD in Internal Medicine & Gastroenterology"
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
            </div>

            <div className="space-y-1.5">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">سنوات الخبرة العملية</label>
              <input
                type="number"
                min="1"
                max="50"
                value={formData.experienceYears}
                onChange={(e) => setFormData({ ...formData, experienceYears: Number(e.target.value) })}
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              />
            </div>

            <div className="flex justify-end pt-4">
              <button
                onClick={() => setCurrentStep(2)}
                className="flex items-center gap-2 rounded-xl bg-[#29508B] px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-[#1E3D6B]"
              >
                الخطوة التالية
                <ArrowLeft className="h-4 w-4" />
              </button>
            </div>
          </div>
        )}

        {currentStep === 2 && (
          <div className="space-y-4">
            <div className="space-y-1.5">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">سعر الاستشارة الأونلاين (بالريال اليمني YER)</label>
              <div className="relative">
                <input
                  type="number"
                  step="1000"
                  value={formData.consultationFeeYer}
                  onChange={(e) => setFormData({ ...formData, consultationFeeYer: Number(e.target.value) })}
                  className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 pl-16 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
                />
                <span className="absolute left-3 top-3 text-xs font-bold text-slate-400">ر.ي YER</span>
              </div>
              <p className="text-[11px] text-slate-500">ملاحظة: تقتطع المنصة عمولة تشغيلية قدرها 15% من كل استشارة ناجحة ومكتملة.</p>
            </div>

            <div className="space-y-1.5">
              <label className="text-xs font-bold text-slate-700 dark:text-slate-300">مدة دقيقة الاستشارة المرئية</label>
              <select
                value={formData.consultationDurationMinutes}
                onChange={(e) => setFormData({ ...formData, consultationDurationMinutes: Number(e.target.value) })}
                className="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm font-medium text-slate-900 focus:border-[#29508B] focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-800/50 dark:text-slate-100 dark:focus:border-blue-500"
              >
                <option value="15">15 دقيقة</option>
                <option value="30">30 دقيقة (موصى بها)</option>
                <option value="45">45 دقيقة</option>
                <option value="60">60 دقيقة</option>
              </select>
            </div>

            <div className="flex items-center justify-between pt-4">
              <button
                onClick={() => setCurrentStep(1)}
                className="flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-700 dark:border-slate-800 dark:text-slate-300"
              >
                <ArrowRight className="h-4 w-4" />
                السابق
              </button>

              <button
                onClick={() => setCurrentStep(3)}
                className="flex items-center gap-2 rounded-xl bg-[#29508B] px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-[#1E3D6B]"
              >
                الخطوة التالية
                <ArrowLeft className="h-4 w-4" />
              </button>
            </div>
          </div>
        )}

        {currentStep === 3 && (
          <div className="space-y-4">
            <div className="rounded-2xl bg-amber-50 p-4 text-xs font-medium text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800 flex items-start gap-2.5">
              <ShieldAlert className="h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" />
              <span>
                يتطلب تفعيل عيادتك رفع نسخة واضحة من ترخيص مزاولة المهنة والهوية الشخصية. لن يتم كشف وثائقك للعلن أبداً بل تُراجع سرّياً بواسطة الإدارة العامة.
              </span>
            </div>

            {[
              { id: 'license_doc', title: 'وثيقة ترخيص مزاولة المهنة الطبية', required: true },
              { id: 'degree_doc', title: 'شهادة البكالوريوس / الدكتوراة الطبية', required: true },
              { id: 'identity_doc', title: 'نسخة الهوية الوطنية / البطاقة الشخصية', required: true },
            ].map((doc) => (
              <div key={doc.id} className="rounded-2xl border border-dashed border-slate-300 p-4 text-center dark:border-slate-700 hover:border-[#29508B]">
                <div className="flex flex-col items-center gap-2">
                  <UploadCloud className="h-6 w-6 text-slate-400" />
                  <span className="text-xs font-bold text-slate-800 dark:text-slate-200">{doc.title}</span>
                  {uploadedFiles[doc.id] ? (
                    <span className="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                      <CheckCircle2 className="h-3.5 w-3.5" />
                      تم اختيار: {uploadedFiles[doc.id]}
                    </span>
                  ) : (
                    <label className="cursor-pointer rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-[#29508B] hover:bg-slate-200 dark:bg-slate-800 dark:text-blue-400">
                      اختر ملف PDF أو صورة
                      <input type="file" accept=".pdf,.png,.jpg,.jpeg" className="hidden" onChange={(e) => handleFileUpload(doc.id, e)} />
                    </label>
                  )}
                </div>
              </div>
            ))}

            <div className="flex items-center justify-between pt-4">
              <button
                onClick={() => setCurrentStep(2)}
                className="flex items-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-bold text-slate-700 dark:border-slate-800 dark:text-slate-300"
              >
                <ArrowRight className="h-4 w-4" />
                السابق
              </button>

              <button
                onClick={handleFinishOnboarding}
                className="flex items-center gap-2 rounded-xl bg-emerald-600 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 hover:bg-emerald-700 active:scale-95 transition-all"
              >
                إرسال الطلب للمراجعة والتوثيق
                <CheckCircle2 className="h-4 w-4" />
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
