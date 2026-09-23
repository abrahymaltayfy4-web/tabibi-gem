import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:patient_app/core/theme/app_colors.dart';

import '../../../../core/widgets/glass_card.dart';
import '../cubit/payment_cubit.dart';
import '../cubit/payment_state.dart';

class CheckoutScreen extends StatelessWidget {
  final int appointmentId;
  final double amountYer;

  const CheckoutScreen({
    super.key,
    required this.appointmentId,
    required this.amountYer,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.darkBackground,
      appBar: AppBar(
        title: const Text('دفع قيمة الاستشارة الطبية'),
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: BlocConsumer<PaymentCubit, PaymentState>(
          listener: (context, state) {
            if (state is PaymentSuccess) {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('تم نجاح عملية الدفع وتأكيد الموعد بنجاح!')),
              );
              Navigator.of(context).pop();
            }
          },
          builder: (context, state) {
            if (state is PaymentLoading) {
              return const Center(
                child: CircularProgressIndicator(color: AppColors.darkPrimary),
              );
            }

            if (state is PaymentError) {
              return Center(
                child: Text(state.message, style: const TextStyle(color: Colors.red)),
              );
            }

            return Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                GlassCard(
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    children: [
                      const Icon(Icons.account_balance_wallet, size: 64, color: AppColors.darkPrimary),
                      const SizedBox(height: 16),
                      const Text(
                        'إجمالي المبلغ المطلوب بالريال اليمني',
                        style: TextStyle(color: AppColors.darkTextSecondary, fontSize: 14),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        '${amountYer.toStringAsFixed(0)} YER',
                        style: const TextStyle(
                          color: AppColors.darkPrimary,
                          fontSize: 32,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),
                const Text(
                  'اختر طريقة الدفع المناسبة:',
                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                ),
                const SizedBox(height: 12),
                ListTile(
                  tileColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  leading: const Icon(Icons.credit_card, color: AppColors.darkPrimary),
                  title: const Text('المحفظة الإلكترونية بالريال اليمني (Sandboxed YER)'),
                  subtitle: const Text('دفع فوري محاكى بالريال اليمني'),
                  trailing: const Icon(Icons.chevron_left),
                  onTap: () {
                    context.read<PaymentCubit>().checkout(appointmentId);
                  },
                ),
                const SizedBox(height: 12),
                ListTile(
                  tileColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  leading: const Icon(Icons.receipt_long, color: Colors.orange),
                  title: const Text('التحويل المصرفي اليدوي (حوالة محلية)'),
                  subtitle: const Text('رفع إشعار التحويل عبر الكريمي أو النجم'),
                  trailing: const Icon(Icons.chevron_left),
                  onTap: () {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('تم اختيار مسار رفع إثبات الدفع اليدوي.')),
                    );
                  },
                ),
                if (state is PaymentCheckoutReady) ...[
                  const SizedBox(height: 32),
                  ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.success,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                    onPressed: () {
                      context.read<PaymentCubit>().confirmPayment(
                        state.paymentInfo.transactionReference,
                      );
                    },
                    child: const Text(
                      'تأكيد الدفع بالريال اليمني (Confirm Payment)',
                      style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                    ),
                  ),
                ],
              ],
            );
          },
        ),
      ),
    );
  }
}
